import * as React from 'react';
import {registerPlugin} from '@wordpress/plugins';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch,} from '@wordpress/data';
import {TextControl, TextareaControl, CheckboxControl} from '@wordpress/components';
import {__} from '@wordpress/i18n';

const SeoFields = (props) => {

    const {postType, postMeta, setPostMeta} = props;

    if (!postType || postType === 'wp_template' || postType === 'wp_template_part') {
        return '';
    }

    return (<PluginDocumentSettingPanel title={__('SEO', 'digitfab-core')} initialOpen={false}>
            <p>
                <TextControl
                    value={postMeta._seo_title}
                    onChange={value => setPostMeta({_seo_title: value})}
                    label={__('SEO-title', 'digitfab-core')}
                />
            </p>

            <p>
                <TextareaControl
                    value={postMeta._seo_desc}
                    onChange={value => setPostMeta({_seo_desc: value})}
                    label={__('SEO-description', 'digitfab-core')}
                />
            </p>

            <p>
                <TextControl
                    value={postMeta._seo_keywords}
                    onChange={value => setPostMeta({_seo_keywords: value})}
                    label={__('Keywords', 'digitfab-core')}
                />
            </p>

            <p>
                <CheckboxControl
                    label={__('Disable indexing', 'digitfab-core')}
                    checked={postMeta._seo_disable_index}
                    onChange={value => setPostMeta({_seo_disable_index: value})}
                />
            </p>
        </PluginDocumentSettingPanel>
    )
}

// @ts-ignore
const SeoMetaBox = compose([

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
])(SeoFields);

registerPlugin('digitfab-seo-metabox', {
    render() {
        // @ts-ignore
        return (<SeoMetaBox/>);
    }
});