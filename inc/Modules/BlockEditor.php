<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class BlockEditor extends Module
{
    public function run(): void
    {
        $this->loader->addFilter('block_categories_all', $this, 'registerCategory');
        $this->loader->addAction('admin_enqueue_scripts', $this, 'enqueueAssets');
    }

    public function getName(): string
    {
        return 'block-editor';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerCategory($categories)
    {
        $categories[] = array(
            'slug'  => 'digitfab',
            'title' => 'Digitfab'
        );

        return $categories;
    }

    public function enqueueAssets()
    {
        $assetFile = include( plugin_dir_path( dirname( __FILE__, 2 ) ) . 'assets/blocks-extensions.min.asset.php' );

        wp_enqueue_script( 'digitfab-blocks-extensions',
            plugin_dir_url( dirname( __FILE__, 2)) . 'assets/blocks-extensions.min.js',
            $assetFile["dependencies"],
            $assetFile["version"]
        );

        wp_set_script_translations( 'digitfab-blocks-extensions', 'digitfab-core', plugin_dir_path(dirname(__DIR__)). 'languages' );
    }
}