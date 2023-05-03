import * as React from "react";
import {render, useEffect, useState} from "@wordpress/element";
import {FC} from "react";
import {TabPanel, Button, Spinner, Notice  } from '@wordpress/components';
import {__} from "@wordpress/i18n";
import GeneralTab, {GeneralResponse} from "./GeneralTab";
import PostTypesTab from "./PostTypeslTab";
import TaxonomiesTab from "./TaxonomiesTab";
import apiFetch from "@wordpress/api-fetch";
import {ADMIN_API_NAMESPACE} from "../../const";

document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("digitfab-seo-settings");
    const token = el?.dataset.token;

    if (!token) return;

    if (el) {
        render(<SeoOptions/>, el);
    }
});

type Alert = {
    type?: "error" | "success" | "warning" | "info"
    text: string
}

type SettingsResponse = {
    seo_general: GeneralResponse
    seo_post_types?: {[key: string]: any}
}

type Config = {
    post_types?: string[]
    taxonomies?: string[]
}

const SeoOptions: FC = (props) => {

    const [loading, setLoading] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [messages, setMessages] = useState<Alert[]>([]);
    const [config, setConfig] = useState<Config>({});

    const [settings, setSettings] = useState<SettingsResponse>({
        seo_general: {}
    })

    useEffect(() => {
        fetchSettings();
    }, []);

    const fetchSettings = async () => {
        if (loading || submitting) return;

        setLoading(true);

        try {
            const responseConfig = await apiFetch<Config>({
                path: `${ADMIN_API_NAMESPACE}/seo/config`
            });

            setConfig(responseConfig)

            const responseSettings = await apiFetch<SettingsResponse>({
                path: `/wp/v2/settings`
            });

            setSettings(responseSettings);
        } catch (e) {
            setMessages([{type: 'error', text: __('An error has occurred while loading the data.', 'digitfab-core')}])
        } finally {
            setLoading(false)
        }
    }

    const handleSubmitClick = () => {

        if (submitting) return;

        setSubmitting(true);

        apiFetch({
            path: `/wp/v2/settings`,
            method: 'post',
            data: settings
        })
            .catch(e => {
                setMessages([{type: 'error', text: __('An error has occurred while submitting the data.', 'digitfab-core')}])
            })
            .finally(() => {
                setMessages([{type: 'success', text: __('Saved successfully.', 'digitfab-core')}])
                setSubmitting(false)
            })
    }

    const handleGeneralChange = (value: { [key: string]: any }) => {
        setSettings({
            ...settings,
            seo_general: {
                ...settings.seo_general,
                ...value
            }
        });
    }

    const handleMessageDeleteClick = (index: number) => {
        setMessages(messages.filter((_, i) => i !== index));
    }

    const TabComponent = (name: string) => {
        switch (name) {
            case 'general':
                return <GeneralTab onChange={handleGeneralChange} {...settings.seo_general} />
            case 'postTypes':
                return <PostTypesTab postTypes={config.post_types} value={settings.seo_post_types || {}}/>
            case 'Taxonomies':
                return <TaxonomiesTab/>
        }
    }

    return (
        <div className={'df-settings'}>
            <h1>{__('SEO', 'digitfab-core')}</h1>
            {
                !!messages.length &&
                <div style={{margin: '0 -15px'}}>
                    {
                        messages.map((message, i) => (
                            <Notice status={message.type} onDismiss={() => handleMessageDeleteClick(i)}>
                                {message.text}
                            </Notice>
                        ))
                    }
                </div>
            }
            <TabPanel
                activeClass="active-tab"
                tabs={[
                    {
                        name: 'general',
                        title: __('General', 'digitfab-core'),
                    },
                    {
                        name: 'postTypes',
                        title: __('Post types', 'digitfab-core'),
                    },
                    {
                        name: 'Taxonomies',
                        title: __('Taxonomies', 'digitfab-core'),
                    },
                ]}
            >
                {(tab) => TabComponent(tab.name)}
            </TabPanel>
            <div>
                <Button
                    variant={'primary'}
                    onClick={handleSubmitClick}
                    disabled={loading || submitting}
                >
                    {__('Save', 'digitfab-core')}
                    {
                        submitting && <Spinner style={{marginTop: '0'}} />
                    }
                </Button>
            </div>
        </div>
    )
}

export default SeoOptions;