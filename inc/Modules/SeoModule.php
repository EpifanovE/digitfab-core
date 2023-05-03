<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class SeoModule extends Module
{
    const CAPABILITY_CODE = 'edit_seo';

    protected ?array $roles = null;

    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerMeta');
        $this->loader->addAction('admin_enqueue_scripts', $this, 'enqueueAdminMetaboxAssets');
        $this->loader->addFilter('wp_sitemaps_add_provider', $this, 'removeSitemapUsers', 10, 2);
    }

    public function getName(): string
    {
        return 'seo-module';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerMeta()
    {
        register_post_meta(null, '_seo_title', [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authSeo']
        ]);

        register_post_meta(null, '_seo_desc', [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authSeo']
        ]);

        register_post_meta(null, '_seo_keywords', [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authSeo']
        ]);

        register_post_meta(null, '_seo_disable_index', [
            'type' => 'boolean',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authSeo']
        ]);
    }

    public function enqueueAdminMetaboxAssets()
    {
        global $post_type;

        if (!in_array($post_type, $this->getSeoPostTypes())) {
            return;
        }

        $metaboxAssetFile = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/seo-metabox.min.asset.php');
        wp_enqueue_script('digitfab-seo-metabox',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/seo-metabox.min.js',
            $metaboxAssetFile["dependencies"],
            $metaboxAssetFile["version"]
        );
        wp_set_script_translations('digitfab-seo-metabox', 'digitfab-core', plugin_dir_path(dirname(__DIR__)) . 'languages');
    }

    public function authSeo()
    {
        return current_user_can(self::CAPABILITY_CODE);
    }

    public function getSeoPostTypes()
    {
        return apply_filters('digitfab/core/seo-post-types', ['post', 'page']);
    }

    public function removeSitemapUsers($provider, $name)
    {
        if( $name === 'users' ){
            return false;
        }

        return $provider;
    }

    protected function getRoles(): array
    {
        if ($this->roles === null) {
            $this->roles = apply_filters('digitfab/core/seo_roles', [
                'admin' => get_role('administrator'),
                'editor' => get_role('editor'),
                'author' => get_role('author'),
                'contributor' => get_role('contributor'),
            ]);
        }

        return $this->roles;
    }

    protected function doActivatePlugin()
    {
        foreach ($this->getRoles() as $role) {
            $role->add_cap(self::CAPABILITY_CODE);
        }
    }

    protected function doDeactivatePlugin()
    {
        foreach ($this->getRoles() as $role) {
            $role->remove_cap(self::CAPABILITY_CODE);
        }
    }
}