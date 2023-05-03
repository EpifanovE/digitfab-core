import * as React from 'react';
import {registerBlockType,} from '@wordpress/blocks';
import {warning} from "@wordpress/icons";
import {
    useBlockProps,
    InnerBlocks,
} from '@wordpress/block-editor';

const BlockEdit = ({attributes, setAttributes,}) => {
    return (
        <div {...useBlockProps()}>
            <InnerBlocks />
        </div>
    );
};

const BlockSave = ({attributes, setAttributes,}) => {
    return <InnerBlocks.Content />
}

registerBlockType('digitfab/cookie-notice', {
    icon: warning,
    edit: BlockEdit,
    save: BlockSave,
});

