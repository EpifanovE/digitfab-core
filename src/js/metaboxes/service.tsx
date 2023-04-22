import React from 'react';
import {registerPlugin} from '@wordpress/plugins';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch,} from '@wordpress/data';
import {TextControl, __experimentalNumberControl as NumberControl, Button,} from '@wordpress/components';
import {__, _x} from '@wordpress/i18n';

const PriceFields = ({postType, postMeta, setPostMeta}) => {

    if (postType !== 'service') {
        return null;
    }

    return (<PluginDocumentSettingPanel title={__('Price', 'digitfab-core')} initialOpen={false}>

            <p>
                <NumberControl
                    label={__('Price', 'digitfab-core')}
                    value={postMeta._price}
                    onChange={(value) => setPostMeta({_price: value})}
                    step={'0.01'}
                    min={0}
                />
            </p>

            <p>
                <TextControl
                    label={_x('Prefix', 'price', 'digitfab-core')}
                    value={postMeta._price_prefix}
                    onChange={(value) => setPostMeta({_price_prefix: value})}
                    help={__('Example: from, to', 'digitfab-core')}
                />
            </p>

            <p>
                <TextControl
                    label={_x('Suffix', 'price', 'digitfab-core')}
                    value={postMeta._price_suffix}
                    onChange={(value) => setPostMeta({_price_suffix: value})}
                    help={__('Example: per hour', 'digitfab-core')}
                />
            </p>

        </PluginDocumentSettingPanel>
    )
}

// @ts-ignore
const PriceMetaBox = compose([

    withSelect((select) => {
        return {
            postMeta: select('core/editor').getEditedPostAttribute('meta'),
            postType: select('core/editor').getCurrentPostType(),
        };
    }),

    withDispatch((dispatch) => {
        return {
            setPostMeta(newMeta) {
                dispatch('core/editor').editPost({meta: newMeta});
            }
        };
    })
])(PriceFields);

registerPlugin('digitfab-service-price-metabox', {
    render() {
        return (<PriceMetaBox/>);
    }
});

const AdvantagesFields = ({postType, postMeta, setPostMeta}) => {

    if (postType !== 'service') {
        return null;
    }

    const {
        _advantages = []
    } = postMeta;

    const handleAddClick = () => {
        setPostMeta({
            _advantages: [
                ..._advantages,
                ''
            ]
        })
    }

    const handleChangeItem = (index: number, value: string) => {
        setPostMeta({
            _advantages: [
                ..._advantages.map((_, i) => {
                    if (i === index) return value;

                    return _;
                }),
            ]
        })
    }

    const handleDeleteClick = (index: number) => {
        setPostMeta({
            _advantages: [
                ..._advantages.filter((_, i) => i !== index),
            ]
        })
    }

    return (
        <PluginDocumentSettingPanel title={__('Advantages', 'digitfab-core')} initialOpen={false}>
            <div className={`string-list-control`}>
                <div className={`string-list-control__list`}>
                    {
                        _advantages.map((item, index) => (
                            <div className={`string-list-control__item`}>
                                <TextControl
                                    value={_advantages[index]}
                                    onChange={(value) => handleChangeItem(index, value)}
                                    className={`string-list-control__field`}
                                />
                                <Button
                                    icon={'no'}
                                    onClick={() => handleDeleteClick(index)}
                                />
                            </div>
                        ))
                    }
                </div>
                <div>
                    <Button
                        onClick={handleAddClick}
                        variant={`secondary`}
                        className={`string-list-control__button`}
                    >
                        {__("Add", 'digitfab-core')}
                    </Button>
                </div>
            </div>
        </PluginDocumentSettingPanel>
    );
};

// @ts-ignore
const AdvantagesMetaBox = compose([

    withSelect((select) => {
        return {
            postMeta: select('core/editor').getEditedPostAttribute('meta'),
            postType: select('core/editor').getCurrentPostType(),
        };
    }),

    withDispatch((dispatch) => {
        return {
            setPostMeta(newMeta) {
                dispatch('core/editor').editPost({meta: newMeta});
            }
        };
    })
])(AdvantagesFields);

registerPlugin('digitfab-service-advantages-metabox', {
    render() {
        return (<AdvantagesMetaBox />);
    }
});