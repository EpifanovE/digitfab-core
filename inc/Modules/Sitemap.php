<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class Sitemap extends Module
{
    public function run(): void
    {
        $this->loader->addAction('template_redirect', $this, 'render');
    }

    public function getName(): string
    {
        return 'sitemap';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function render()
    {
        if (!preg_match('/sitemap\.xml/', $_SERVER['REQUEST_URI'])) {
            return;
        }

        status_header(200);
        header('Content-Type: application/xml; charset=utf-8');
        print('<?xml version="1.0" encoding="utf-8" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>http://www.example.com/page1.html</loc></url></urlset>');
        exit();
    }

    protected function getPostTypes(): array
    {
        return apply_filters('digitfab/core/sitemap_post_types', [
            'post', 'page',
        ]);
    }

    protected function getTaxonomies(): array
    {
        return apply_filters('digitfab/core/sitemap_taxonomies', [
            'tag', 'category',
        ]);
    }
}