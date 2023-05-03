<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules\CF7;

use Digitfab\Core\Modules\Module;
use WPCF7_ContactForm;
use WPCF7_Submission;

if (!defined('ABSPATH')) {
    die;
}

class DatabaseMessages extends Module
{
    public function run(): void
    {
        $this->loader->addAction('wpcf7_mail_sent', $this, 'saveMessage');
        $this->loader->addFilter('wpcf7_editor_panels', $this, 'addDatabaseSettingsTab');
        $this->loader->addAction('wpcf7_save_contact_form', $this, 'databaseSettingSave', 10, 3);
        $this->loader->addFilter('wpcf7_pre_construct_contact_form_properties', $this, 'addProps', 10, 2);
        $this->loader->addFilter('admin_menu', $this, 'changeMenu');
    }

    public function getName(): string
    {
        return 'cf7-database-messages';
    }

    /**
     * Сохранение сообщения при отправке пользователем в виде записи типа: df-message
     *
     * @return void
     */
    public function saveMessage(): void
    {
        $submission  = WPCF7_Submission::get_instance();
        $postedData = $submission->get_posted_data();

        do_action('digitfab/core/save_message', $postedData);

        $templatePostMeta = get_post_meta($submission->get_contact_form()->id(), '_wpcf7_df_database_template', true);

        if (empty($templatePostMeta)) {
            return;
        }

        $enabledFieldValue = get_post_meta($submission->get_contact_form()->id(), '_wpcf7_df_database_enabled', true);

        if (empty($enabledFieldValue)) {
            return;
        }

        $messagePostData = [
            'post_title'    => wp_date('j F Y H:i'),
            'post_content'  => wpcf7_mail_replace_tags($templatePostMeta, $postedData),
            'post_status'   => 'publish',
            'post_author' => 0,
            'post_type' => 'df-message'
        ];

        $messagePostId = wp_insert_post( $messagePostData );

        update_post_meta($messagePostId, '_wpcf7_df_database_form_id', $submission->get_contact_form()->id());
    }

    /**
     * Добавление панели настроек сохранения сообщения в БД на страницу настроек формы
     *
     * @param $panels
     * @return array
     */
    public function addDatabaseSettingsTab($panels): array
    {
        $panels['df-database'] = [
            'title' => __('Database', 'digitfab-core'),
            'callback' => [$this, 'settingsTabTemplate'],
        ];

        return $panels;
    }

    /**
     * Отображение содержимого панели настроек
     *
     * @return void
     */
    public function settingsTabTemplate(): void
    {
        $postId = $_GET['post'] ?? null;

        if (empty($postId)) {
            return;
        }

        $form = WPCF7_ContactForm::get_instance($postId);

        $templateContent = $form->prop('df_database_template');
        $enabledField = $form->prop('df_database_enabled');

        ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="wpcf7-df-database-enabled">Включено</label>
                </th>
                <td>
                    <input type="checkbox" name="wpcf7-df-database[enabled]" id="wpcf7-df-database-enabled"<?php checked($enabledField); ?>>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wpcf7-df-database-template">Шаблон</label>
                </th>
                <td>
                    <textarea id="wpcf7-df-database-template" name="wpcf7-df-database[template]" cols="100" rows="18" class="large-text code"><?php echo $templateContent; ?></textarea>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * Добавление дополнительных параметров для формы при сохранении в БД
     *
     * @param WPCF7_ContactForm $contactForm
     * @param $args
     * @param $context
     * @return void
     */
    public function databaseSettingSave($contactForm, $args, $context): void
    {
        $props = $contactForm->get_properties();

        $props['df_database_template'] = $args['wpcf7-df-database']['template'] ?? '';
        $props['df_database_enabled'] = !empty($args['wpcf7-df-database']['enabled']);

        $contactForm->set_properties($props);

    }

    /**
     * Добавление дополнительных параметров для формы
     *
     * @param $props
     * @param $form
     * @return mixed
     */
    public function addProps($props, $form)
    {
        $props['df_database_template'] = '';
        $props['df_database_enabled'] = '';

        return $props;
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
            if ($menuItem[0] === 'Contact Form 7') {
                $menuItem[0] = __('Forms', 'digitfab-core');
                $menu[$key] = $menuItem;
            }
        }
    }
}