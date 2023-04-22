<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class TitleBlock extends Module
{
    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerBlock');
        $this->loader->addFilter('get_the_archive_title_prefix', $this, 'removePrefix');
    }

    public function getName(): string
    {
        return 'title-block';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerBlock(): void
    {
        $asset_file = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/block-title.min.asset.php');

        wp_register_script(
            'digitfab-block-title',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/block-title.min.js',
            $asset_file['dependencies'],
            $asset_file['version']
        );

        register_block_type('digitfab/title', [
            'editor_script' => 'digitfab-block-title',
            "render_callback" => [$this, 'blockRenderCallback'],
            'api_version' => 2,
            "category" => "digitfab",
            "supports" => [
                "align" => ["wide", "full"],
                "anchor" => true,
                "className" => true,
                "color" => [
                    "gradients" => true,
                    "link" => true,
                    "__experimentalDefaultControls" => [
                        "background" => true,
                        "text" => true
                    ]
                ],
                "spacing" => [
                    "margin" => true,
                    "padding" => true
                ],
                "typography" => [
                    "fontSize" => true,
                    "lineHeight" => true,
                    "__experimentalFontFamily" => true,
                    "__experimentalFontStyle" => true,
                    "__experimentalFontWeight" => true,
                    "__experimentalLetterSpacing" => true,
                    "__experimentalTextTransform" => true,
                    "__experimentalTextDecoration" => true,
                    "__experimentalDefaultControls" => [
                        "fontSize" => true,
                        "fontAppearance" => true,
                        "textTransform" => true
                    ]
                ],
                "__unstablePasteTextInline" => true,
                "__experimentalSlashInserter" => true
            ],
            "title" => __('Title', 'digitfab-core'),
            'attributes' => [
                "textAlign" => [
                    "type" => "string"
                ],
                "content" => [
                    "type" => "string",
                    "source" => "html",
                    "selector" => "h1,h2,h3,h4,h5,h6",
                    "default" => "",
                    "__experimentalRole" => "content"
                ],
                "level" => [
                    "type" => "number",
                    "default" => 2
                ],
                "placeholder" => [
                    "type" => "string"
                ]
            ],
        ]);
    }

    public function blockRenderCallback($attributes): string
    {
        $title = dfGetTitle();

        if (!$title) {
            return '';
        }

        $tag_name = 'h2';

        if (isset($attributes['level'])) {
            $tag_name = 0 === $attributes['level'] ? 'p' : 'h' . $attributes['level'];
        }

        $classes = [];

        if (isset($attributes['textAlign'])) {
            $classes[] = 'has-text-align-' . $attributes['textAlign'];
        }

        if (isset($attributes['style']['elements']['link']['color']['text'])) {
            $classes[] = 'has-link-color';
        }

        $wrapper_attributes = get_block_wrapper_attributes(array('class' => implode(' ', $classes)));

        return sprintf(
            '<%1$s %2$s>%3$s</%1$s>',
            $tag_name,
            $wrapper_attributes,
            $title
        );
    }

    public function removePrefix($prefix): string
    {
        return '';
    }
}