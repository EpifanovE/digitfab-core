import * as React from "react";
import {FC} from "react";
import {FormTokenField, BaseControl} from '@wordpress/components';
import { decodeEntities } from '@wordpress/html-entities';
import {useSelect} from '@wordpress/data';
import {store as coreStore} from '@wordpress/core-data';
import {useState, useEffect} from '@wordpress/element';
import {useDebounce} from '@wordpress/compose';

type ValueType = Array<number>;

type SelectPostsProps = {
    postType: string
    value: ValueType
    onChange: (value: ValueType) => void
    maxLength?: number
    exclude?: ValueType
    label?: string
    help?: string
}

type PostData = {
    id: number
    title: {
        rendered: string
    }
}

type Suggestion = {
    id: number
    value: string
}

const EMPTY_ARRAY = [];
const BASE_QUERY = {
    order: 'asc',
    _fields: 'id,title',
    context: 'view',
};

const getPostIdByPostValue = (posts, postValue) => {

    const postId = postValue?.id || posts?.find((term) => decodeEntities(term.title.rendered) === postValue)?.id;

    if (postId) {
        return postId;
    }

    const postValueLower = postValue.toLocaleLowerCase();

    return posts?.find(
        (post) => decodeEntities(post.title.rendered).toLocaleLowerCase() === postValueLower
    )?.id;
};

const SelectPosts: FC<SelectPostsProps> = (props) => {

    const {
        postType,
        value: postIds,
        onChange,
        label,
        help,
        maxLength,
        exclude = []
    } = props;

    const [search, setSearch] = useState('');
    const [value, setValue] = useState<Suggestion[]>(EMPTY_ARRAY);
    const [suggestions, setSuggestions] = useState<string[]>(EMPTY_ARRAY);
    const debouncedSearch = useDebounce(setSearch, 250);

    const {searchResults, searchHasResolved} = useSelect(
        (select) => {
            if (!search) {
                return {searchResults: EMPTY_ARRAY, searchHasResolved: true};
            }

            // @ts-ignore
            const {getEntityRecords, hasFinishedResolution} = select(coreStore);

            const selectorArgs = [
                'postType',
                postType,
                {
                    ...BASE_QUERY,
                    search,
                    orderby: 'title',
                    exclude: [...postIds, ...exclude],
                    per_page: 20,
                },
            ];

            return {
                // @ts-ignore
                searchResults: getEntityRecords(...selectorArgs),
                searchHasResolved: hasFinishedResolution(
                    'getEntityRecords',
                    selectorArgs
                ),
            };
        },
        [search, postIds]
    );

    // @ts-ignore
    const existingPosts: PostData[] = useSelect(
        (select) => {
            if (!postIds?.length) return EMPTY_ARRAY;
            const {getEntityRecords} = select(coreStore);
            return getEntityRecords('postType', postType, {
                ...BASE_QUERY,
                include: postIds,
                per_page: postIds.length,
            });
        },
        [postIds]
    );

    useEffect(() => {

        if (!postIds?.length) {
            setValue(EMPTY_ARRAY);
        }

        if (!existingPosts?.length) return;

        const sanitizedValue: Suggestion[] = postIds.reduce((accumulator: Suggestion[], id) => {
            const entity = existingPosts.find((post) => post.id === id);

            if (entity) {
                accumulator.push({
                    id,
                    value: decodeEntities(entity.title?.rendered),
                });
            }

            return accumulator;
        }, []);

        setValue(sanitizedValue);

    }, [postIds, existingPosts]);

    useEffect(() => {
        if (!searchHasResolved) return;
        setSuggestions((searchResults as PostData[]).map(result => decodeEntities(result.title.rendered)));
    }, [searchResults, searchHasResolved]);

    const onPostsChange = (newPostValues) => {
        const newPostIds = new Set();

        for (const postValue of newPostValues) {
            const postId = getPostIdByPostValue(searchResults, postValue);
            if (postId) {
                newPostIds.add(postId);
            }
        }

        setSuggestions(EMPTY_ARRAY);
        onChange(Array.from(newPostIds) as ValueType);
    };

    return (
        <BaseControl {...props} label={false}>
            <div className="block-library-query-inspector__taxonomy-control">
                <FormTokenField
                    label={label}
                    value={value}
                    onInputChange={debouncedSearch}
                    suggestions={suggestions.map(item => decodeEntities(item))}
                    onChange={onPostsChange}
                    __experimentalShowHowTo={false}
                    maxLength={maxLength}
                />
            </div>
        </BaseControl>
    );
}

export default SelectPosts;