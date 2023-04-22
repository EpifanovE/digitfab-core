import React, {FC, useEffect, useState} from "react";
import {__experimentalScrollable as Scrollable, Button, TextControl, BaseControl, __experimentalDivider as Divider, DropdownMenu  } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {__} from '@wordpress/i18n';
import {
    moreVertical,
    close,
    arrowUp,
    arrowDown,
} from '@wordpress/icons';

type Item = {
    id: number
    title: {
        rendered: string
    }
}

type PostRelationValue = Array<number>;

type PostRelationProps = {
    postType: string
    value: PostRelationValue
    onChange: (value: PostRelationValue) => void
    multiple?: boolean
    exclude?: PostRelationValue
}

type QueryMeta = {
    page?: number
    search?: string
}

const PER_PAGE = 10;

const PostRelation: FC<PostRelationProps> = (props) => {

    const {postType, exclude = [], value, onChange, multiple = false} = props;

    const [items, setItems] = useState<Item[]>([]);
    const [selectedItemsData, setSelectedItemsData] = useState<Item[]>([]);

    const [loading, setLoading] = useState(false);
    const [isEnd, setIsEnd] = useState(false);
    const [search, setSearch] = useState('');

    const [queryMeta, setQueryMeta] = useState<QueryMeta>({
        page: 1,
        search: ''
    })

    const [prevQueryMeta, setPrevQueryMeta] = useState<QueryMeta>();

    useEffect(() => {
        if (queryMeta.search !== prevQueryMeta?.search) {
            fetchItems(true);
            return;
        }

        fetchItems();
    }, [queryMeta]);

    useEffect(() => {
        setIsEnd(false);
    }, [queryMeta.search]);

    useEffect(() => {
        fetchItemsData();
    }, [value]);

    const fetchItems = (clear: boolean = false) => {
        if (loading) return;

        if (queryMeta.page === prevQueryMeta?.page && queryMeta.search === prevQueryMeta?.search) return;

        setLoading(true);

        const queryParams = {
            _fields: [
                'id',
                'title',
            ],
            page: queryMeta.page,
            search: queryMeta.search
        }

        apiFetch<Item[]>({path: addQueryArgs(`/wp/v2/${postType}`, queryParams)})
            .then(response => {

                if (clear) {
                    setItems(response);
                } else {
                    setItems([
                        ...items,
                        ...response
                    ]);
                }

                setPrevQueryMeta({...queryMeta});

                if (response.length < PER_PAGE) {
                    setIsEnd(true);
                }

            })
            .finally(() => {
                setLoading(false);
            });
    }

    const fetchItemsData = () => {

        const queryParams = {
            _fields: [
                'id',
                'title',
            ],
            include: value,
            per_page: 100
        }

        apiFetch<Item[]>({path: addQueryArgs(`/wp/v2/${postType}`, queryParams)})
            .then(response => {
                setSelectedItemsData(response)
            })
            .finally(() => {

            });
    }

    const handleMoreClick = () => {
        if (isEnd) return;

        setQueryMeta({
            ...queryMeta,
            page: (queryMeta.page || 1) + 1
        });
    }

    const handleCheckClick = (id: number) => {
        if (value.includes(id)) {
            onChange([...value.filter(item => item !== id)]);
            return;
        }

        if (multiple) {
            onChange([...value, id]);
            return;
        } else {
            onChange([id]);
        }
    }

    const handleSearchClick = () => {
        setQueryMeta({
            page: 1,
            search: search
        });
    }

    const handleClearClick = () => {
        onChange([]);
    }

    const handleRemoveClick = (id: number) => {
        onChange([...value.filter(item => item !== id)]);
    }

    const handleUpClick = (index: number) => {
        if (index === 0) return;

        let nextArray = [...value];
        nextArray.splice(index - 1, 0, nextArray.splice(index, 1)[0]);

        onChange(nextArray);
    }

    const handleDownClick = (index: number) => {
        if (index === value.length - 1) return;

        let nextArray = [...value];
        nextArray.splice(index + 1, 0, nextArray.splice(index, 1)[0]);

        onChange(nextArray);
    }

    const getTitleById = (id: number): string => {
        const items = selectedItemsData.filter(item => item.id === id);

        if (!items.length) return '';

        return items[0].title.rendered;
    }

    return (
        <div className={'post-relation-control'}>
            {
                !!value.length &&
                <>
                    <BaseControl
                        label={__('Selected', 'digitfab-core')}
                    >
                        <div className={'post-relation-control__selected-list'}>
                            <Scrollable style={{maxHeight: 250}}>
                                {
                                    value.map((item, index) => (
                                        <div
                                            className={`post-relation-control__item`}
                                        >
                                            <div>{item}</div>
                                            <div dangerouslySetInnerHTML={{__html: getTitleById(item)}}/>
                                            <DropdownMenu
                                                icon={ moreVertical }
                                                className={'post-relation-control__item-button'}
                                                controls={ [
                                                    {
                                                        title: __('Move up', 'digitfab-core'),
                                                        icon: arrowUp,
                                                        onClick: () => handleUpClick(index),
                                                    },
                                                    {
                                                        title: __('Move down', 'digitfab-core'),
                                                        icon: arrowDown,
                                                        onClick: () => handleDownClick(index),
                                                    },
                                                    {
                                                        title: __('Remove', 'digitfab-core'),
                                                        icon: close,
                                                        onClick: () => handleRemoveClick(item),
                                                    },
                                                ] }
                                            />

                                        </div>
                                    ))
                                }
                            </Scrollable>
                        </div>
                        {
                            !!value.length &&
                            <div style={{marginTop: '8px'}}>
                                <Button
                                    onClick={handleClearClick}
                                    variant={`secondary`}
                                    className={`post-relation-control__button`}
                                >
                                    {__("Clear", 'digitfab-core')}
                                </Button>
                            </div>
                        }
                    </BaseControl>

                    <Divider />
                </>
            }

            <BaseControl
                label={__('Available', 'digitfab-core')}
            >
                <div className={`post-relation-control__filter`}>
                    <TextControl
                        value={search}
                        onChange={value => setSearch(value)}
                        placeholder={__('Search', 'digitfab-core')}
                        className={`post-relation-control__search-field`}
                    />
                    <Button
                        icon={'search'}
                        onClick={handleSearchClick}
                    />
                </div>
                <div className={'post-relation-control__list'}>
                    <Scrollable style={{maxHeight: 250}}>
                        {
                            items
                                .filter(item => !exclude.includes(item.id))
                                .filter(item => !value.includes(item.id))
                                .map(item => (
                                <div
                                    className={`post-relation-control__item`}
                                    onClick={() => handleCheckClick(item.id)}
                                >
                                    <div>{item.id}</div>
                                    <div dangerouslySetInnerHTML={{__html: item.title.rendered}}/>
                                </div>
                            ))
                        }
                    </Scrollable>
                </div>
                {
                    !isEnd &&
                    <div>
                        <Button
                            onClick={handleMoreClick}
                            variant={`secondary`}
                            className={`post-relation-control__button`}
                        >
                            {__("Load more", 'digitfab-core')}
                        </Button>
                    </div>
                }
            </BaseControl>
        </div>
    );
}

export default PostRelation;