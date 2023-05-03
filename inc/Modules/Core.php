<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class Core extends Module
{
    public function run(): void
    {
        $this->loader->addAction('admin_enqueue_scripts', $this, 'enqueueAdminAssets');
        $this->loader->addAction('wp_enqueue_scripts', $this, 'enqueueAssets');
        $this->loader->addFilter('posts_where', $this, 'addQueryParams', 10, 2);
        $this->loader->addAction('pre_get_posts', $this, 'modifyAdminSearch');

        add_editor_style(plugin_dir_url(dirname(__FILE__, 2)) . 'assets/front.css');
    }

    public function getName(): string
    {
        return 'core';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function enqueueAdminAssets()
    {
        $asset_file = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/admin.min.asset.php');

        wp_enqueue_style(
            'digitfab-admin',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/admin.css',
            [],
            $asset_file["version"]
        );

        wp_enqueue_script('digitfab-admin',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/admin.min.js',
            $asset_file["dependencies"],
            $asset_file["version"]
        );

        wp_set_script_translations('digitfab-admin', 'digitfab-core', plugin_dir_path(dirname(__DIR__)) . 'languages');
    }

    public function enqueueAssets()
    {
        $asset_file = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/front.min.asset.php');

        wp_enqueue_script('digitfab-core',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/front.min.js',
            $asset_file["dependencies"],
            $asset_file["version"],
            true,
        );

        wp_enqueue_style(
            'digitfab-core',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/front.css',
            [],
            $asset_file["version"]
        );
    }

    /**
     * Добавление параметров запроса title_filter и title_filter_relation
     *
     * Параметры нужны для добавления возможности одновременного поиска по заголовку и доп. полям
     */
    public function addQueryParams($where, $wp_query)
    {
        global $wpdb;
        if ($search_term = $wp_query->get('title_filter')) :
            $search_term = $wpdb->esc_like($search_term);
            $search_term = ' \'%' . $search_term . '%\'';
            $title_filter_relation = (strtoupper($wp_query->get('title_filter_relation')) === 'OR' ? 'OR' : 'AND');
            $where .= ' ' . $title_filter_relation . ' ' . $wpdb->posts . '.post_title LIKE ' . $search_term;
        endif;

        return $where;
    }

    public function modifyAdminSearch($query)
    {
        if (!is_admin()) {
            return;
        }

        if (!$query->is_main_query() && !$query->is_search()) {
            return;
        }

        $searchString = get_query_var('s');

        if (!filter_var($searchString, FILTER_VALIDATE_INT)) {
            return;
        }

        $query->set('p', intval($searchString));

        $query->set('s', '');
    }
}