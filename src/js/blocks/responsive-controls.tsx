import * as React from 'react';
import {InspectorControls} from '@wordpress/block-editor';
import {
    Panel, PanelBody, PanelRow,
    Button, ButtonGroup, BaseControl, Icon
} from '@wordpress/components';
import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import {mobile, tablet, desktop} from '@wordpress/icons';

const isBlockForHideControls = () => {
    return true;
}

const HidingClassMap = {
    mobile: 'hide-on-mobile',
    tablet: 'hide-on-tablet',
    desktop: 'hide-on-desktop',
}

export const withHideControls = (BlockEdit) => (props) => {

    if (!isBlockForHideControls()) {
        return <BlockEdit {...props} />
    }

    const {
        attributes,
        setAttributes,
    } = props;

    const handleClick = (code: string) => {

        const classes = attributes.className?.split(' ').map(item => item.trim()) || [];

        if (classes.includes(HidingClassMap[code])) {
            setAttributes({className: classes.filter(item => item !== HidingClassMap[code]).join(' ')})
        } else {
            setAttributes({className: [...classes, HidingClassMap[code]].join(' ')})
        }
    }

    const isActive = (code: string): boolean => {
        const classes = attributes.className?.split(' ').map(item => item.trim()) || [];
        return classes.includes(HidingClassMap[code])
    }

    return (
        <>
            <BlockEdit {...props} />
            <InspectorControls>
                <Panel>
                    <PanelBody title={__('Responsive control', 'digitfab-core')} initialOpen={ false }>
                        <PanelRow>
                            <BaseControl label={__('Hide on', 'digitfab-core')}>
                                <div>
                                    <ButtonGroup>
                                        <Button variant={isActive('mobile') ? 'primary' : 'secondary'} onClick={() => handleClick('mobile')} title={__('Mobile', 'digitfab-core')}>
                                            <Icon icon={mobile} />
                                        </Button>

                                        <Button variant={isActive('tablet') ? 'primary' : 'secondary'} onClick={() => handleClick('tablet')} title={__('Tablet', 'digitfab-core')}>
                                            <Icon icon={tablet} />
                                        </Button>

                                        <Button variant={isActive('desktop') ? 'primary' : 'secondary'} onClick={() => handleClick('desktop')} title={__('Desktop', 'digitfab-core')}>
                                            <Icon icon={desktop} />
                                        </Button>
                                    </ButtonGroup>
                                </div>
                            </BaseControl>
                        </PanelRow>
                    </PanelBody>
                </Panel>
            </InspectorControls>
        </>
    );
};

addFilter('editor.BlockEdit', 'digitfab/core/hide-controls', withHideControls);