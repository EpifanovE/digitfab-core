<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class QueryLoopBlock extends Module
{
    protected $parsedBlock = null;

    public function run(): void
    {
        $this->loader->addAction('admin_enqueue_scripts', $this, 'registerBlockVariation');
        $this->loader->addFilter('pre_render_block', $this, 'addQueryFilter', 10, 3);
        $this->loader->addFilter('rest_service_query', $this, 'changeRest', 10, 2);
        $this->loader->addFilter('rest_post_query', $this, 'changeRest', 10, 2);
    }

    public function getName(): string
    {
        return 'query-loop';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerBlockVariation()
    {
        $asset_file = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/block-query-loop.min.asset.php');

        wp_enqueue_script('digitfab-query-loop',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/block-query-loop.min.js',
            $asset_file["dependencies"],
            $asset_file["version"]
        );
    }

    public function addQueryFilter($preRender, $parsedBlock, $parentBlock)
    {
        if ($parsedBlock['blockName'] === 'core/query') {
            remove_filter('query_loop_block_query_vars', [$this, 'changeQuery']);
            $this->parsedBlock = $parsedBlock;
            add_filter('query_loop_block_query_vars', [$this, 'changeQuery']);
        }

        return $preRender;
    }

    public function changeQuery($query)
    {
        global $post;

        if (!empty($this->parsedBlock['attrs']['query']['dfIncluded'])) {
            $query['post__in'] = $this->parsedBlock['attrs']['query']['dfIncluded'];
            unset($query['order']);
            $query['orderby'] = 'post__in';
        } else if (!empty($post)) {
            $query['post__not_in'] = [$post->ID];
        }

        return $query;
    }

    public function changeRest($args, $request)
    {
        if (!empty($request['dfIncluded'])) {
            $args['post__in'] = $request['dfIncluded'];
            $args['orderby'] = 'post__in';
        }

        return $args;
    }
}