import * as React from 'react';
import {registerBlockType,} from '@wordpress/blocks';
import {page} from "@wordpress/icons";
import {
    useBlockProps,
    InspectorControls
} from '@wordpress/block-editor';
import {
    TextControl,
    Panel,
    PanelBody,
} from '@wordpress/components';
import {__} from '@wordpress/i18n';
import { useSelect} from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import {useEffect} from "react";

const BlockEdit = (props) => {

    const {attributes, setAttributes, context} = props;
    const {postType, postId} = context;

    const postData = useSelect(
        ( select ) => {
            const { getEditedEntityRecord } = select( coreStore );
            return  getEditedEntityRecord(
                'postType',
                postType,
                postId
            );
        },
        [ postType, postId ]
    );

    useEffect(() => console.log(postData), [postData]);

    return (
        <div {...useBlockProps()}>
            <InspectorControls key="setting">
                <Panel>
                    <PanelBody title={__('Field', 'digitfab-core')} initialOpen={true}>
                        <TextControl
                            label={__('Field name', 'digitfab-core')}
                            value={attributes.name}
                            onChange={value => setAttributes({name: value})}
                        />
                    </PanelBody>
                </Panel>
            </InspectorControls>

            {postData.meta[attributes.name] ?? ''}
        </div>
    );
};

registerBlockType('digitfab/meta-field', {
    icon: page,
    edit: BlockEdit
});

