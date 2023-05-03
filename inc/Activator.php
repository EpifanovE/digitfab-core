<?php

declare(strict_types=1);

namespace Digitfab\Core;

if (!defined('ABSPATH')) {
    die;
}

class Activator
{
    protected Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function activate(): void
    {
        foreach ($this->plugin->getModules() as $moduleName) {
            $module = $this->plugin->get($moduleName);
            $module->onActivatePlugin();
        }
    }

    public function deactivate(): void
    {
        foreach ($this->plugin->getModules() as $moduleName) {
            $module = $this->plugin->get($moduleName);
            $module->onDeactivatePlugin();
        }
    }
}