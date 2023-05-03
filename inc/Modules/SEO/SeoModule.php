<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules\SEO;

use Digitfab\Core\Loader;
use Digitfab\Core\Modules\Module;
use WP_REST_Response;

if (!defined('ABSPATH')) {
    die;
}

class SeoModule extends Module
{
    const CAPABILITY_CODE = 'edit_seo';

    protected ?array $roles = null;

    private string $apiNamespace;

    public function __construct(Loader $loader, string $apiNamespace)
    {
        parent::__construct($loader);
        $this->apiNamespace = $apiNamespace;
    }

    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerMeta');
        $this->loader->addAction('admin_enqueue_scripts', $this, 'enqueueAdminMetaboxAssets');
        $this->loader->addAction('admin_enqueue_scripts', $this, 'enqueueAdminOptionsAssets');
        $this->loader->addAction('admin_menu', $this, 'registerOptionsPage');
        $this->loader->addAction('rest_api_init', $this, 'registerOptions');
        $this->loader->addAction('rest_api_init', $this, 'registerAdminRoutes');
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

        $metaboxAssetFile = include(plugin_dir_path(dirname(__FILE__, 3)) . 'assets/seo-metabox.min.asset.php');
        wp_enqueue_script('digitfab-seo-metabox',
            plugin_dir_url(dirname(__FILE__, 3)) . 'assets/seo-metabox.min.js',
            $metaboxAssetFile["dependencies"],
            $metaboxAssetFile["version"]
        );
        wp_set_script_translations('digitfab-seo-metabox', 'digitfab-core', plugin_dir_path(dirname(__DIR__)) . 'languages');
    }

    public function enqueueAdminOptionsAssets()
    {
        global $pagenow;

        if ($pagenow !== 'options-general.php') {
            return;
        }

        $optionsAssetFile = include(plugin_dir_path(dirname(__FILE__, 3)) . 'assets/seo-options.min.asset.php');
        wp_enqueue_script('digitfab-seo-options',
            plugin_dir_url(dirname(__FILE__, 3)) . 'assets/seo-options.min.js',
            $optionsAssetFile["dependencies"],
            $optionsAssetFile["version"]
        );
        wp_set_script_translations('digitfab-seo-options', 'digitfab-core', plugin_dir_path(dirname(__DIR__)) . 'languages');
        wp_enqueue_style('wp-edit-blocks');
        foreach ($optionsAssetFile['dependencies'] as $style) {
            wp_enqueue_style($style);
        }
    }

    public function authSeo()
    {
        return current_user_can(self::CAPABILITY_CODE);
    }

    public function registerOptionsPage()
    {
        add_submenu_page(
            'options-general.php',
            __('SEO', 'digitfab-core'),
            __('SEO', 'digitfab-core'),
            self::CAPABILITY_CODE,
            'digitfab-seo-options',
            [$this, 'renderOptionsPage'],
            50
        );
    }

    public function registerOptions()
    {
        register_setting(
            'seo-general-settings',
            'seo_general',
            [
                'type' => 'object',
                'default' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => [
                                'type' => 'string',
                            ],
                            'description' => [
                                'type' => 'string',
                            ],
                            'keywords' => [
                                'type' => 'string',
                            ],
                        ]
                    ],
                ],
            ]);
    }

    public function registerAdminRoutes()
    {
        register_rest_route($this->apiNamespace, 'seo/config', array(
            'methods' => 'GET',
            'callback' => [$this, 'getSeoAdminParams'],
            'permission_callback' => [$this, 'authSeo']
        ));
    }

    public function getSeoAdminParams()
    {
        return new WP_REST_Response([
            'post_types' => $this->getSeoPostTypes(),
            'taxonomies' => $this->getSeoTaxonomies(),
        ], 200);
    }

    public function getSeoPostTypes()
    {
        return apply_filters('digitfab/core/seo-post-types', ['post', 'page']);
    }

    public function getSeoTaxonomies()
    {
        return apply_filters('digitfab/core/seo-taxonomies', ['category', 'tag']);
    }

    public function renderOptionsPage()
    {
        echo '<div id="digitfab-seo-settings" data-token="' . wp_create_nonce('wp_rest') . '"></div>';
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