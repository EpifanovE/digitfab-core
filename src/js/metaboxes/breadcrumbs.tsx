import React, {useEffect} from 'react';
import {registerPlugin} from '@wordpress/plugins';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch,} from '@wordpress/data';
import {SelectControl} from '@wordpress/components';
import {__} from '@wordpress/i18n';
import {useTaxonomies} from "../hooks/useTaxonomies";

const BreadcrumbsFields = (props) => {

    const {postType, postMeta, postTerms, setPostMeta} = props;

    const taxonomies = useTaxonomies(postType);

    useEffect(() => {
        if (!postTerms) {
            return;
        }

        if (!postMeta?._df_breadcrumbs_term) {
            return;
        }

        if (!postTerms?.length) {
            setPostMeta({
                _df_breadcrumbs_term: null
            });
            return;
        }

        const match = postTerms.some(term => term.id === postMeta._df_breadcrumbs_term);

        if (!match) {
            setPostMeta({
                _df_breadcrumbs_term: null
            });
        }

    }, [postTerms]);

    if (!postType || postType === 'wp_template' || postType === 'wp_template_part') {
        return '';
    }

    const handleChangeTaxonomy = (value: string) => {
        setPostMeta(
            {
                _df_breadcrumbs_taxonomy_slug: value,
                _df_breadcrumbs_taxonomy_rest: taxonomies.filter(tax => tax.slug === value)[0]?.rest_base,
                _df_breadcrumbs_term: null
            });
    }

    const handleChangeTerm = (value: string) => {
        setPostMeta({_df_breadcrumbs_term: !!value ? parseInt(value) : null})
    }

    return (<PluginDocumentSettingPanel title={__('Breadcrumbs', 'digitfab-core')} initialOpen={false}>

            <SelectControl
                label={__('Taxonomy', 'digitfab-core')}
                value={postMeta?._df_breadcrumbs_taxonomy_slug}
                options={[
                    {
                        value: '',
                        label: __('No', 'digitfab-core')
                    },
                    ...(taxonomies || []).map(tax => (
                        {
                            value: tax.slug,
                            label: tax.name
                        }
                    ))
                ]}
                onChange={handleChangeTaxonomy}
                disabled={!taxonomies?.length}
            />

            {
                !!postMeta?._df_breadcrumbs_taxonomy_slug &&
                <SelectControl
                    label={__('Term', 'digitfab-core')}
                    value={postMeta?._df_breadcrumbs_term}
                    options={[
                        {
                            value: '',
                            label: __('No', 'digitfab-core')
                        },
                        ...(postTerms || []).map(term => (
                            {
                                value: term.id,
                                label: term.name
                            }
                        ))
                    ]}
                    onChange={handleChangeTerm}
                    disabled={!taxonomies?.length}
                />
            }

        </PluginDocumentSettingPanel>
    )
}

// @ts-ignore
const BreadcrumbsMetaBox = compose([

    withSelect((select) => {
        const taxonomyRest = select('core/editor').getEditedPostAttribute('meta')['_df_breadcrumbs_taxonomy_rest'];
        const taxonomySlug = select('core/editor').getEditedPostAttribute('meta')['_df_breadcrumbs_taxonomy_slug'];

        const postTerms = select('core').getEntityRecords('taxonomy', taxonomySlug, {include: select('core/editor').getEditedPostAttribute(taxonomyRest)});

        return {
            postMeta: select('core/editor').getEditedPostAttribute('meta'),
            postType: select('core/editor').getCurrentPostType(),
            postTerms: postTerms,
        };
    }),

    withDispatch((dispatch) => {
        return {
            setPostMeta(newMeta) {
                dispatch('core/editor').editPost({meta: newMeta});
            }
        };
    })
])(BreadcrumbsFields);

registerPlugin('digitfab-breadcrumbs-metabox', {
    render() {
        // @ts-ignore
        return (<BreadcrumbsMetaBox />);
    }
});