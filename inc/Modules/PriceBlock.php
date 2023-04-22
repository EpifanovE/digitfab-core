<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class PriceBlock extends Module
{
    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerBlock');
    }

    public function getName(): string
    {
        return 'price-block';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerBlock(): void
    {
        $asset_file = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/block-price.min.asset.php');

        wp_register_script(
            'digitfab-block-price',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/block-price.min.js',
            $asset_file['dependencies'],
            $asset_file['version']
        );

        register_block_type('digitfab/price', [
            'editor_script' => 'digitfab-block-price',
            "render_callback" => [$this, 'blockRenderCallback'],
            'api_version' => 2,
            "category" => "digitfab",
            "supports" => [],
            "title" => __('Price', 'digitfab-core'),
            'attributes' => [
                'postId' => [
                    'type' => 'integer',
                ]
            ],
            'uses_context' => [ 'postId', 'postType' ],
        ]);
    }

    public function blockRenderCallback($attributes, $content, $block): string
    {
        $postId = $block->context['postId'] ?? $attributes['postId'];
        $post = get_post($postId);

        if (empty($post)) {
            return '';
        }

        return dfGetPrice($post);
    }
}