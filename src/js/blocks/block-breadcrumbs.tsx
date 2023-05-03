import * as React from 'react';
import {registerBlockType,} from '@wordpress/blocks';
import {navigation} from "@wordpress/icons";
import {
    useBlockProps,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

const BlockEdit = ({attributes, setAttributes,}) => {
    return (
        <div {...useBlockProps()}>
            <ServerSideRender
                block="digitfab/breadcrumbs"
                attributes={{...attributes, editorView: true}}
            />
        </div>
    );
};

registerBlockType('digitfab/breadcrumbs', {
    icon: navigation,
    edit: BlockEdit,
});

