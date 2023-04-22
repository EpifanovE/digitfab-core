<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class RankMath extends Module
{
    public function run(): void
    {
        $this->loader->addFilter('admin_menu', $this, 'changeMenu');
        $this->loader->addAction('init', $this, 'registerBlock');
    }

    public function getName(): string
    {
        return 'rank-math';
    }

    /**
     * Изменения текста в меню админ-панели
     *
     * @return void
     */
    public function changeMenu()
    {
        global $menu;

        foreach ($menu as $key => $menuItem) {
            if ($menuItem[0] === 'Rank Math') {
                $menuItem[0] = 'SEO';
                $menu[$key] = $menuItem;
            }
        }
    }

    public function registerBlock(): void
    {
        $assetFile = include(plugin_dir_path(dirname(__FILE__, 2)) . 'assets/block-breadcrumb.min.asset.php');

        wp_register_script(
            'digitfab-block-breadcrumb',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/block-breadcrumb.min.js',
            $assetFile['dependencies'],
            $assetFile['version']
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

    public function blockRenderCallback($attrs)
    {
        if (!empty($attrs['editorView'])) {
            $html = '';
            $html .= '<nav aria-label="breadcrumbs" class="rank-math-breadcrumb">';
            $html .= '<p><a href="#">Home</a><span class="separator"> » </span><a href="#">Category</a><span class="separator"> » </span><a href="#">SubCategory</a></p>';
            $html .= '</nav>';
            return $html;
        }

        return do_shortcode('[rank_math_breadcrumb]');
    }
}