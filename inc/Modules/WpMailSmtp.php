<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

if (!defined('ABSPATH')) {
    die;
}

class WpMailSmtp extends Module
{
    public function run(): void
    {
        $this->loader->addFilter('admin_menu', $this, 'changeMenu', 11);
    }

    public function getName(): string
    {
        return 'wp-mail-smtp';
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
            if ($menuItem[0] === 'WP Mail SMTP') {
                $menuItem[0] = 'SMTP';
                $menu[$key] = $menuItem;
            }
        }
    }
}