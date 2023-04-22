import * as React from 'react';
import {registerBlockVariation} from '@wordpress/blocks';
import {loop} from "@wordpress/icons";
import {InspectorControls} from '@wordpress/block-editor';
import {
    __experimentalToolsPanel as ToolsPanel,
    __experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';
import {addFilter} from '@wordpress/hooks';
import {__, _x} from '@wordpress/i18n';
import SelectPosts from "../components/SelectPosts";

const MY_VARIATION_NAME = 'digitfab/query-loop';

registerBlockVariation('core/query', {
        name: MY_VARIATION_NAME,
        title: __('Service query loop', 'digitfab-core'),
        description: '',
        category: 'digitfab',
        isActive: ({namespace, query}) => {
            return (
                namespace === MY_VARIATION_NAME && query.postType === 'service'
            );
        },
        icon: loop,
        attributes: {
            namespace: MY_VARIATION_NAME,
            query: {
                perPage: 6,
                pages: 0,
                offset: 0,
                postType: 'service',
                order: 'desc',
                orderBy: 'date',
                author: '',
                search: '',
                exclude: [],
                sticky: '',
                inherit: false
            },
            displayLayout: {
                type: "flex",
                columns: 3
            },
        },
        scope: ['inserter'],
        allowedControls: ['inherit', 'order', 'sticky', 'taxQuery', 'search'],
        innerBlocks: [
            [
                'core/post-template',
                {},
                [
                    ['core/post-featured-image'],
                    [
                        'core/post-title',
                        {
                            isLink: true,
                        }
                    ],
                    ['digitfab/price'],
                    [
                        'core/read-more',
                        {
                            content: _x('Read more', 'button', 'digitfab-core')
                        },
                    ],
                ],
            ],
        ],
    }
);

const isQueryLoop = (props) => {
    return props.name === 'core/query';
}

export const withAdvancedQueryControls = (BlockEdit) => (props) => {

    const {
        attributes,
        setAttributes,
    } = props;

    const {query} = attributes;

    return isQueryLoop(props) ? (
        <>
            <BlockEdit {...props} />
            <InspectorControls>
                <ToolsPanel
                    className="block-library-query-toolspanel__filters"
                    label={__('Adv. filters', 'digitfab-core')}
                    resetAll={() => setAttributes({
                        ...attributes,
                        query: {
                            ...query,
                            dfIncluded: []
                        }
                    })}
                >
                    <ToolsPanelItem
                        label={__('Selected records', 'digitfab-core')}
                        hasValue={() => !!query.dfIncluded?.length}
                        onDeselect={() => setAttributes({
                            ...attributes,
                            query: {
                                ...query,
                                dfIncluded: []
                            }
                        })}
                    >
                        <SelectPosts
                            value={query.dfIncluded || []}
                            onChange={value => setAttributes({
                                ...attributes,
                                query: {
                                    ...query,
                                    dfIncluded: value
                                }
                            })}
                            postType={attributes.query.postType}
                            label={__('Select records', 'digitfab-core')}
                            help={__('Only the specified entries will be shown.', 'digitfab-core')}
                        />
                    </ToolsPanelItem>
                </ToolsPanel>
            </InspectorControls>
        </>
    ) : (
        <BlockEdit {...props} />
    );
};

addFilter('editor.BlockEdit', 'digitfab/core/query-loop', withAdvancedQueryControls);
