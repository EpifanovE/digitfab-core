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
use Digitfab\Core\Modules\CF7\DatabaseMessages;
use Digitfab\Core\Modules\CookieNotice;
use Digitfab\Core\Modules\Core;
use Digitfab\Core\Modules\Messages;
use Digitfab\Core\Modules\MetaFieldBlock;
use Digitfab\Core\Modules\PriceBlock;
use Digitfab\Core\Modules\QueryLoopBlock;
use Digitfab\Core\Modules\Services;
use Digitfab\Core\Modules\TitleBlock;
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
    \Digitfab\Core\Modules\RankMath::class,
    WpMailSmtp::class,
    CookieNotice::class,
    Services::class,
    MetaFieldBlock::class,
    PriceBlock::class,
    TitleBlock::class,
    QueryLoopBlock::class,
]);

$digitfabCorePlugin->singleton(Loader::class, new Loader());

function digitfabCore(): Plugin
{
    global $digitfabCorePlugin;
    return $digitfabCorePlugin;
}

do_action('digitfab/core/loaded');

add_action('after_setup_theme', function () use ($digitfabCorePlugin) {
    $digitfabCorePlugin->run();
});

$activator = new Activator();

register_activation_hook(__FILE__, function () use ($activator) {
    $activator->activate();
});

register_deactivation_hook(__FILE__, function () use ($activator) {
    $activator->deactivate();
});
