<?php

declare(strict_types=1);

namespace Digitfab\Core;

use Digitfab\Core\Modules\Module;

if (!defined('ABSPATH')) {
    die;
}

class Plugin extends Container
{
    /**
     * @template Type
     * @var array<class-string<Type>>
     */
    protected array $modules;

    public function __construct(array $modules = [])
    {
        $this->loadTextdomain();
        $this->modules = $modules;
    }

    public function run()
    {
        foreach ($this->modules as $module) {

            $moduleObject = $this->get($module);

            if (!$moduleObject->isEnabled()) {
                continue;
            }

            if (!empty($moduleObject) && $moduleObject instanceof Module) {
                $moduleObject->run();
            }
        }

        $this->get(Loader::class)->run();
    }

    public function loadTextdomain() {
        load_plugin_textdomain(
            'digitfab-core',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    /**
     * @template Type
     * @return  array<class-string<Type>>
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}