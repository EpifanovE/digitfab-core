import * as React from "react";
import {FC, useEffect} from "react";
import {__} from "@wordpress/i18n";
import {TextControl, TextareaControl, Card, CardBody} from "@wordpress/components";

type PostTypesTabProps = {
    postTypes?: string[]
    value?: {[key: string]: any}
}

const PostTypesTab: FC<PostTypesTabProps> = (props) => {

    const {
        postTypes,
        value
    } = props;

    useEffect(() => console.log(postTypes), [postTypes])

    const handleChange = (postType: string, value: {[key: string]: any}) => {

    }

    return (
        <div style={{padding: '30px 0'}}>
            {
                postTypes?.map(postType => (
                    <PostTypeSettings
                        postType={postType}
                        onChange={handleChange}
                        value={value ? value[postType] : undefined}
                    />
                ))
            }
        </div>
    )
}

export default PostTypesTab;

type PostTypeSettingsProps = {
    postType: string
    value: {
        title?: string
        description?: string
        keywords?: string
    }
    onChange: (postType: string, value: {[key: string]: any}) => void
}

const PostTypeSettings: FC<PostTypeSettingsProps> = (props) => {

    const {
        postType,
        value,
        onChange
    } = props;

    const handleChange = (fieldValue: {[key: string]: any}) => {
        onChange(postType, {
            ...value,
            ...fieldValue
        })
    }

    return (
        <Card>
            <CardBody>
                <TextControl
                    value={value?.title || ''}
                    onChange={value => handleChange({title: value})}
                    label={__('Tag <title>', 'digitfab-core')}
                    className={`df-settings__row`}
                    help={__('Available variables: %entity_title%, %site_title%', 'digitfab-core')}
                />

                <TextareaControl
                    value={value?.description || ''}
                    onChange={value => handleChange({description: value})}
                    label={__('Tag <meta name="description">', 'digitfab-core')}
                    className={`df-settings__row`}
                />

                <TextControl
                    value={value?.keywords || ''}
                    onChange={value => handleChange({keywords: value})}
                    label={__('Tag <meta name="keywords">', 'digitfab-core')}
                />
            </CardBody>
        </Card>
    );
}