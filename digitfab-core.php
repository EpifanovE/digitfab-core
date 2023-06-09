<?php
/**
 * Plugin Name:         Digitfab Core
 * Plugin URI:          https://digitfab.ru
 * Description:         Base functions
 * Version:             1.0.0
 * Requires at least:   6.2
 * Requires PHP:        8.1
 * Author:              Evgeny Epifanov
 * Author URI:          https://digitfab.ru
 * Text Domain:         digitfab-core
 * Domain Path:         /languages
 */

use Digitfab\Core\Activator;
use Digitfab\Core\Loader;
use Digitfab\Core\Modules\BlockEditor;
use Digitfab\Core\Modules\Breadcrumbs;
use Digitfab\Core\Modules\CF7\DatabaseMessages;
use Digitfab\Core\Modules\CookieNotice;
use Digitfab\Core\Modules\Core;
use Digitfab\Core\Modules\Messages;
use Digitfab\Core\Modules\MetaFieldBlock;
use Digitfab\Core\Modules\SeoModule;
use Digitfab\Core\Modules\WpMailSmtp;
use Digitfab\Core\Plugin;

if (!defined('ABSPATH')) {
    die;
}

require_once 'vendor/autoload.php';
require_once 'inc/functions.php';

$digitfabCorePlugin = new Plugin([
    Core::class,
    BlockEditor::class,
    Messages::class,
    DatabaseMessages::class,
    Breadcrumbs::class,
    WpMailSmtp::class,
    CookieNotice::class,
    MetaFieldBlock::class,
    SeoModule::class,
]);

$digitfabCorePlugin->singleton(Loader::class, new Loader());

$digitfabCorePlugin->set('api_namespace', 'digitfab/v1');

function digitfabCore(): Plugin
{
    global $digitfabCorePlugin;
    return $digitfabCorePlugin;
}

do_action('digitfab/core/loaded');

add_action('after_setup_theme', function () use ($digitfabCorePlugin) {
    $digitfabCorePlugin->run();
});

$activator = new Activator($digitfabCorePlugin);

register_activation_hook(__FILE__, function () use ($activator) {
    $activator->activate();
});

register_deactivation_hook(__FILE__, function () use ($activator) {
    $activator->deactivate();
});
