import * as React from 'react';
import classnames from 'classnames';
import {registerBlockType,} from '@wordpress/blocks';
import {heading} from "@wordpress/icons";
import { __ } from '@wordpress/i18n';
import {
    AlignmentControl,
    BlockControls,
    useBlockProps,
} from '@wordpress/block-editor';

import HeadingLevelDropdown from './heading-level-dropdown';

const BlockEdit = ({
                       attributes,
                       setAttributes,
                       style,
                   }) => {
    const { textAlign, level, } = attributes;
    const TagName = 'h' + level;
    const blockProps = useBlockProps( {
        className: classnames( {
            [ `has-text-align-${ textAlign }` ]: textAlign,
        } ),
        style,
    } );

    let titleElement = (
        <TagName { ...blockProps }>{ __( 'Page title', 'digitfab-core' ) }</TagName>
    );

    return (
        <>
            <BlockControls group="block">
                <HeadingLevelDropdown
                    selectedLevel={ level }
                    onChange={ ( newLevel ) =>
                        setAttributes( { level: newLevel } )
                    }
                />
                <AlignmentControl
                    value={ textAlign }
                    onChange={ ( nextAlign ) => {
                        setAttributes( { textAlign: nextAlign } );
                    } }
                />
            </BlockControls>
            {titleElement}
        </>
    );
};

registerBlockType('digitfab/title', {
    icon: heading,
    edit: BlockEdit
});

