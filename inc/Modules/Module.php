<?php

declare(strict_types=1);

namespace Digitfab\Core\Modules;

use Digitfab\Core\Loader;

if (!defined('ABSPATH')) {
    die;
}

abstract class Module
{
    protected Loader $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    abstract public function run(): void;

    abstract public function getName(): string;

    public function isEnabled(): bool
    {
        return apply_filters('digitfab/core/' . $this->getName(), false);
    }
}