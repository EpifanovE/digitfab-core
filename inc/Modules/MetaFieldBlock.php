<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class MetaFieldBlock extends Module
{
    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerBlock');
    }

    public function getName(): string
    {
        return 'meta-field-block';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerBlock()
    {
        $asset_file = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/block-meta-field.min.asset.php');

        wp_register_script(
            'digitfab-block-meta-field',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/block-meta-field.min.js',
            $asset_file['dependencies'],
            $asset_file['version']
        );

        register_block_type('digitfab/meta-field', [
            'editor_script' => 'digitfab-block-meta-field',
            "render_callback" => [$this, 'blockRenderCallback'],
            'api_version' => 2,
            "category" => "digitfab",
            "supports" => [],
            "title" => __('Meta field value', 'digitfab-core'),
            'attributes' => [
                'name' => [
                    'type' => 'string',
                ],
                'postId' => [
                    'type' => 'integer',
                ]
            ],
            'uses_context' => [ 'postId', 'postType' ],
        ]);
    }

    public function blockRenderCallback($attributes, $content, $block)
    {
        $postId = $block->context['postId'] ?? $attributes['postId'];

        if (empty($attributes['name'])) {
            return '';
        }

        return get_post_meta($postId, $attributes['name'], true);
    }
}