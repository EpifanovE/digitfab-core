<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class CookieNotice extends Module
{

    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerBlock');
    }

    public function getName(): string
    {
        return 'cookie-notice';
    }

    public function registerBlock()
    {
        $asset_file = include( plugin_dir_path( dirname( __FILE__, 2 ) ) . 'assets/block-cookie-notice.min.asset.php' );

        wp_register_script(
            'digitfab-block-cookie-notice',
            plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/block-cookie-notice.min.js',
            $asset_file['dependencies'],
            $asset_file['version']
        );

        register_block_type( 'digitfab/cookie-notice', [
            'editor_script'   => 'digitfab-block-cookie-notice',
            "style"           => "digitfab-core",
            "render_callback" => [$this, 'blockRenderCallback'],
            'api_version'     => 2,
            "category"        => "digitfab",
            "supports" => [
                "color" => [
                    "text" => true,
                    "background" => true
                ],
                "spacing" => [
                    "margin" => true,
                    "padding" => true,
                    "blockGap" => true,
                ],
            ],
            "title" => __('Cookie notice', 'digitfab-core'),
            'attributes'      => [

            ],
        ] );

        wp_set_script_translations( 'digitfab-block-cookie-notice', 'digitfab-core', plugin_dir_path(dirname(__DIR__)). 'languages' );
    }

    public function blockRenderCallback($attributes, $content)
    {
        if (!empty($_COOKIE['df-hide-cookie-notice'])) {
            return '';
        }

        $wrapperAttributes = get_block_wrapper_attributes();

        $output = "<div " . $wrapperAttributes . ">";
        $output .= $content;
        $output .= "</div>";

        return $output;
    }
}