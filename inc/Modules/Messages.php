<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class Messages extends Module
{
    public function run(): void
    {
        $this->loader->addAction('init', $this, 'registerPostType');
        $this->loader->addAction('add_meta_boxes', $this, 'addMessageMetaBox');
        $this->loader->addAction('save_post', $this, 'savePost');
        $this->loader->addFilter('manage_df-message_posts_columns', $this, 'columns');
        $this->loader->addAction('manage_df-message_posts_custom_column', $this, 'columnsData', 10, 2);
    }

    public function getName(): string
    {
        return 'messages';
    }

    public function registerPostType(): void
    {
        register_post_type('df-message', [
                'labels' => [
                    'name' => __('Messages', 'digitfab-core'),
                    'singular_name' => __('Message', 'digitfab-core'),
                    'menu_name' => __('Messages', 'digitfab-core'),
                    'add_new' => _x('Add new', 'message', 'digitfab-core'),
                    'add_new_item' => __('Add new message', 'digitfab-core'),
                    'edit_item' => __('Edit message', 'digitfab-core'),
                    'new_item' => __('New message', 'digitfab-core'),
                    'view_item' => __('View message', 'digitfab-core'),
                    'search_items' => __('Search messages', 'digitfab-core'),
                    'not_found' => __('Messages not found', 'digitfab-core'),
                    'not_found_in_trash' => __('Messages not found', 'digitfab-core'),
                ],
                'public' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_rest' => false,
                'publicly_queryable' => false,
                'menu_position' => 20,
                'has_archive' => false,
                'supports' => ['title',],
                'rewrite' => false,
                'menu_icon' => 'dashicons-email',
                'capability_type' => 'message',
                'map_meta_cap' => true,
                'delete_with_user' => false,
            ]
        );
    }

    public function addMessageMetaBox()
    {
        add_meta_box('digitfab-database-message-metabox', __('Message', 'digitfab-core'), [$this, 'metaboxTemplate'], 'df-message', 'normal', 'high',
            [
                '__back_compat_meta_box' => false,
            ]
        );
    }

    public function metaboxTemplate($post)
    {
        wp_nonce_field("save_message", "_df_message_content_nonce");
        ?>
        <textarea id="digitfab-db-message" name="digitfab-db-message[content]" rows="7" class="large-text"><?php echo $post->post_content; ?></textarea>
        <?php
    }

    public function savePost($postId)
    {
        if (!isset($_POST['digitfab-db-message'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_df_message_content_nonce'], "save_message")) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        foreach ($_POST["digitfab-db-message"] as $key => $value) {
            if ($key === 'content' && ! wp_is_post_revision( $postId )) {
                remove_action('save_post', [$this, 'savePost']);
                wp_update_post([
                    'ID' => $postId,
                    'post_content' => wp_kses_post($value),
                ]);
                add_action('save_post', [$this, 'savePost']);
            }

            update_post_meta($postId, sanitize_text_field($key), sanitize_text_field($value));
        }
    }

    public function columns($columns)
    {
        $date = $columns['date'];
        $title = $columns['title'];
        unset($columns['date']);
        unset($columns['title']);
        $columns['id'] = 'ID';
        $columns['title'] = $title;
        $columns['date'] = $date;

        return $columns;
    }

    public function columnsData($column, $postId)
    {
        switch ($column) :

            case 'id' :
                echo '<div>' . $postId . '</div>';
                break;

        endswitch;
    }
}