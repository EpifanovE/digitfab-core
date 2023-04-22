<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

use WP_Post;

if (!defined('ABSPATH')) {
    die;
}

class Services extends Module
{
    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerPostType');
        $this->loader->addAction('init', $this, 'registerTaxonomy');
        $this->loader->addAction('init', $this, 'registerServiceMeta');
        $this->loader->addFilter('manage_service_posts_columns', $this, 'servicesColumns');
        $this->loader->addAction('manage_service_posts_custom_column', $this, 'servicesColumnsData', 10, 2);
        $this->loader->addAction('rest_api_init', $this, 'addCurrencyToRest');
    }

    public function getName(): string
    {
        return 'services';
    }

    public function registerPostType(): void
    {
        register_post_type('service', [
                'labels' => [
                    'name' => __('Services', 'digitfab-core'),
                    'singular_name' => __('Service', 'digitfab-core'),
                    'menu_name' => __('Services', 'digitfab-core'),
                    'add_new' => _x('Add new', 'service', 'digitfab-core'),
                    'add_new_item' => __('Add new service', 'digitfab-core'),
                    'edit_item' => __('Edit service', 'digitfab-core'),
                    'new_item' => __('New service', 'digitfab-core'),
                    'view_item' => __('View service', 'digitfab-core'),
                    'search_items' => __('Search services', 'digitfab-core'),
                    'not_found' => __('Services not found', 'digitfab-core'),
                    'not_found_in_trash' => __('Services not found', 'digitfab-core'),
                ],
                'public' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_rest' => true,
                'publicly_queryable' => true,
                'menu_position' => 20,
                'has_archive' => true,
                'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields'],
                'rewrite' => [
                    'slug' => 'uslugi',
                ],
                'menu_icon' => 'dashicons-book',
                'capability_type' => 'service',
                'map_meta_cap' => true,
                'delete_with_user' => false,
            ]
        );
    }

    public function registerTaxonomy(): void
    {
        register_taxonomy('service_category', ['service'], [
            'labels' => [
                'name' => __('Categories', 'digitfab-core'),
                'singular_name' => __('Category', 'digitfab-core'),
                'search_items' => __('Search categories', 'digitfab-core'),
                'all_items' => __('All categories', 'digitfab-core'),
                'view_item ' => __('View category', 'digitfab-core'),
                'parent_item' => __('Parent category', 'digitfab-core'),
                'parent_item_colon' => __('Parent category:', 'digitfab-core'),
                'edit_item' => __('Edit category', 'digitfab-core'),
                'update_item' => __('Update category', 'digitfab-core'),
                'add_new_item' => __('Add new category', 'digitfab-core'),
                'new_item_name' => __('New category name', 'digitfab-core'),
                'menu_name' => __('Categories', 'digitfab-core'),
                'back_to_items' => __('Back to categories', 'digitfab-core'),
            ],
            'description' => __('Service category', 'digitfab-core'),
            'public' => true,
            'hierarchical' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'kategorii-uslug',
                'hierarchical' => true,
                'with_front' => false
            ],
            'capabilities' => [
                'manage_terms' => 'edit_services',
                'edit_terms' => 'edit_services',
                'delete_terms' => 'edit_services',
                'assign_terms' => 'edit_services',
            ],
            'show_admin_column' => true,
            'show_in_rest' => true,
            'show_in_quick_edit' => true,
        ]);
    }

    public function registerServiceMeta(): void
    {
        register_post_meta('service', '_price', [
            'type' => 'number',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authMetaFields']
        ]);

        register_post_meta('service', '_price_prefix', [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authMetaFields']
        ]);

        register_post_meta('service', '_price_suffix', [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => [$this, 'authMetaFields']
        ]);

        register_post_meta('service', '_upsell', [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => [
                'schema' => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'number',
                    ],
                ],
            ],
            'auth_callback' => [$this, 'authMetaFields']
        ]);

        register_post_meta('service', '_advantages', [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => [
                'schema' => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'auth_callback' => [$this, 'authMetaFields']
        ]);
    }

    public function authMetaFields(): bool
    {
        return current_user_can('edit_services');
    }

    public function servicesColumns($columns)
    {
        $date = $columns['date'];
        $title = $columns['title'];
        $category = $columns['taxonomy-service_category'];
        unset($columns['date']);
        unset($columns['title']);
        unset($columns['taxonomy-service_category']);
        $columns['id'] = 'ID';
        $columns['title'] = $title;
        $columns['taxonomy-service_category'] = $category;
        $columns['price'] = __('Price', 'digitfab-core');
        $columns['date'] = $date;

        return $columns;
    }

    public function servicesColumnsData($column, $postId)
    {
        switch ($column) :

            case 'id' :
                echo '<div>' . $postId . '</div>';
                break;

            case 'price' :
                echo dfGetPrice(get_post($postId));
                break;

        endswitch;
    }

    public function addCurrencyToRest() {
        register_rest_field(
            'service',
            'df_currency',
            [
                'get_callback'    => function () {
                    return dfGetCurrency();
                },
                'update_callback' => null,
                'schema'          => null,
            ]
        );
    }
}