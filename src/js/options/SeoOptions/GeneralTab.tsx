import * as React from "react";
import {FC, } from "react";
import {TextControl, TextareaControl, Card, CardBody} from "@wordpress/components";
import {__} from "@wordpress/i18n";

export type GeneralResponse = {
    title?: string
    description?: string
    keywords?: string
}

type GeneralTabProps = {
    onChange: (value: {[key: string]: any}) => void
} & GeneralResponse

const GeneralTab: FC<GeneralTabProps> = (props) => {

    const {
        title,
        description,
        keywords,
        onChange
    } = props;

    return (
        <div style={{padding: '30px 0'}}>
                <Card>
                    <CardBody>
                        <TextControl
                            value={title || ''}
                            onChange={value => onChange({title: value})}
                            label={__('Tag <title>', 'digitfab-core')}
                            className={`df-settings__row`}
                            help={__('Available variables: %entity_title%, %site_title%', 'digitfab-core')}
                        />

                        <TextareaControl
                            value={description || ''}
                            onChange={value => onChange({description: value})}
                            label={__('Tag <meta name="description">', 'digitfab-core')}
                            className={`df-settings__row`}
                        />

                        <TextControl
                            value={keywords || ''}
                            onChange={value => onChange({keywords: value})}
                            label={__('Tag <meta name="keywords">', 'digitfab-core')}
                        />
                    </CardBody>
                </Card>
        </div>
    )
}

export default GeneralTab;