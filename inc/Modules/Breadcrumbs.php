<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

use WP_Term;

if (!defined('ABSPATH')) {
    die;
}

class Breadcrumbs extends Module
{
    protected array $postTypes = [
        'post',
    ];

    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerBlock');
        $this->loader->addAction('init', $this, 'registerPostMeta');
        $this->loader->addAction('admin_enqueue_scripts', $this, 'enqueueAdminAssets');
    }

    public function getName(): string
    {
        return 'breadcrumbs';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function registerBlock(): void
    {
        $blockAssetFile = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/block-breadcrumbs.min.asset.php');

        wp_register_script(
            'digitfab-block-breadcrumb',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/block-breadcrumbs.min.js',
            $blockAssetFile['dependencies'],
            $blockAssetFile['version']
        );

        register_block_type('digitfab/breadcrumb', [
            'editor_script' => 'digitfab-block-breadcrumb',
            "render_callback" => [$this, 'blockRenderCallback'],
            'api_version' => 2,
            "category" => "digitfab",
            "supports" => [
                "align" => ["wide", "full"],
                "anchor" => true,
                "className" => true,
                "color" => [
                    "gradients" => true,
                    "link" => true,
                    "__experimentalDefaultControls" => [
                        "background" => true,
                        "text" => true
                    ]
                ],
                "spacing" => [
                    "margin" => true,
                    "padding" => true
                ],
                "typography" => [
                    "fontSize" => true,
                    "lineHeight" => true,
                    "__experimentalFontFamily" => true,
                    "__experimentalFontStyle" => true,
                    "__experimentalFontWeight" => true,
                    "__experimentalLetterSpacing" => true,
                    "__experimentalTextTransform" => true,
                    "__experimentalTextDecoration" => true,
                    "__experimentalDefaultControls" => [
                        "fontSize" => true,
                        "fontAppearance" => true,
                        "textTransform" => true
                    ]
                ],
                "__unstablePasteTextInline" => true,
                "__experimentalSlashInserter" => true
            ],
            "title" => __('Breadcrumb', 'digitfab-core'),
            'attributes' => [
                'editorView' => [
                    'type' => 'boolean',
                    'default' => false
                ]
            ],
        ]);
    }

    public function enqueueAdminAssets()
    {
        global $post_type;

        if (!in_array($post_type, apply_filters('digitfab/core/breadcrumbs-post-types', $this->postTypes))) {
            return;
        }

        $metaboxAssetFile = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/breadcrumbs-metabox.min.asset.php');

        wp_enqueue_script( 'digitfab-breadcrumbs-metabox',
            plugin_dir_url( dirname( __FILE__, 2)) . 'assets/breadcrumbs-metabox.min.js',
            $metaboxAssetFile["dependencies"],
            $metaboxAssetFile["version"]
        );

        wp_set_script_translations( 'digitfab-breadcrumbs-metabox', 'digitfab-core', plugin_dir_path(dirname(__DIR__)). 'languages' );
    }

    public function registerPostMeta(): void
    {
        foreach (apply_filters('digitfab/core/breadcrumbs-post-types', $this->postTypes) as $postType) {
            register_post_meta($postType, '_df_breadcrumbs_taxonomy_rest', [
                'type' => 'string',
                'single' => true,
                'show_in_rest' => true,
                'auth_callback' => [$this, 'authMetaFields']
            ]);

            register_post_meta($postType, '_df_breadcrumbs_taxonomy_slug', [
                'type' => 'string',
                'single' => true,
                'show_in_rest' => true,
                'auth_callback' => [$this, 'authMetaFields']
            ]);

            register_post_meta($postType, '_df_breadcrumbs_term', [
                'type' => 'number',
                'single' => true,
                'show_in_rest' => true,
                'auth_callback' => [$this, 'authMetaFields']
            ]);
        }
    }

    public function authMetaFields(): bool
    {
        return current_user_can('edit_posts');
    }

    public function blockRenderCallback($attrs)
    {
        if (!empty($attrs['editorView'])) {
            $html = '';
            $html .= '<nav aria-label="breadcrumbs" class="rank-math-breadcrumb">';
            $html .= '<p><a href="#">Home</a><span class="separator"> » </span><a href="#">Category</a><span class="separator"> » </span><a href="#">SubCategory</a></p>';
            $html .= '</nav>';
            return $html;
        }

        $pages = [
            [
                'text' => apply_filters('digitfab/core/breadcrumbs_home_label', _x('Home', 'breadcrumbs', 'digitfab-core')),
                'link' => home_url(),
            ],
        ];

        if (is_tag() || is_tax() || is_category()) {
            $currentTerm = get_queried_object();
            $terms = [];
            $this->buildParents($currentTerm, $terms);

            if (is_category() || is_tag()) {
                $pages[] = [
                    'text' => get_the_title( get_option('page_for_posts', true) ),
                    'link' => get_post_type_archive_link( 'post' )
                ];
            }

            $pages = array_merge($pages, array_reverse($terms));
        }

        $currentPost = get_post();

        if (is_singular('post')) {
            $pages[] = [
                'text' => get_the_title( get_option('page_for_posts', true) ),
                'link' => get_post_type_archive_link( 'post' )
            ];

            $categories = get_the_category($currentPost->ID);

            if (count($categories) === 1) {
                $terms = [];
                $this->buildParents($categories[0], $terms);
                $pages = array_merge($pages, array_reverse($terms));
            }
        }

        if (is_singular('page') && !empty($currentPost)) {
            $parentPages = [];
            $this->buildPageParents($currentPost, $parentPages);
            $pages = array_merge($pages, array_reverse($parentPages));
        }

        $pages = apply_filters('digitfab/core/breadcrumbs_pages', $pages, $this);

        if (!apply_filters('digitfab/core/breadcrumbs_display_last', true)) {
            unset($pages[count($pages) - 1]);
        } else {
            unset($pages[count($pages) - 1]['link']);
        }

        return $this->render($pages);
    }

    public function render(array $pages)
    {
        $wrapperAttributes = get_block_wrapper_attributes();

        $result = array_reduce($pages, function ($acc, $item) {
            $acc['html'] .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="wp-block-digitfab-breadcrumb__item">';

            if (!empty($item['link'])) {
                $acc['html'] .= '<a href="' . $item['link'] . '" itemprop="item">';
                $acc['html'] .= '<span itemprop="name">' . $item['text'] . '</span>';
                $acc['html'] .= '</a>';
            } else {
                $acc['html'] .= '<span itemprop="name">' . $item['text'] . '</span>';
            }

            $acc['html'] .= '<meta itemprop="position" content="' . $acc['position'] .'" >';
            $acc['html'] .= '</li>';

            $acc['position']++;
            return $acc;
        }, ['html' => sprintf('<ol itemscope itemtype="http://schema.org/BreadcrumbList" %1$s>', $wrapperAttributes), 'position' => 1]);

        $result['html'] .= '</ol>';

        return $result['html'];
    }

    public function buildParents(WP_Term $currentTerm, array &$items)
    {
        $items[] = [
            'text' => $currentTerm->name,
            'link' => get_term_link($currentTerm)
        ];

        if (empty($currentTerm->parent)) {
            return;
        }

        $parentTerm = get_term($currentTerm->parent);
        $this->buildParents($parentTerm, $items);
    }

    public function buildPageParents(\WP_Post $currentPost, array &$items)
    {
        $items[] = [
            'text' => $currentPost->post_title,
            'link' => get_permalink($currentPost)
        ];

        if (empty($currentPost->post_parent)) {
            return;
        }

        $parentPost = get_post($currentPost->post_parent);
        $this->buildPageParents($parentPost, $items);
    }
}