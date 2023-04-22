<?php

declare(strict_types=1);

namespace Digitfab\Core;

use Digitfab\Core\Modules\Module;
use Exception;

if (!defined('ABSPATH')) {
    die;
}

abstract class Container
{
    protected array $definitions = [];

    protected array $singletons = [];

    protected array $cache = [];

    /**
     * @template Type
     * @param class-string<Type> $key
     * @return Type
     * @throws Exception
     */
    public function get(string $key)
    {
        $definition = $this->definitions[$key] ?? null;

        if (in_array($key, $this->singletons)) {
            if (isset($this->cache[$key])) {
                return $this->cache[$key];
            }

            if (is_string($definition) && class_exists($definition)) {
                $this->cache[$key] = new $definition();
                return $this->cache[$key];
            }

            if (is_callable($definition)) {
                $this->cache[$key] = $definition($this);
                return $this->cache[$key];
            }

            $this->cache[$key] = $definition;

            return $this->cache[$key];
        }

        if (is_string($definition) && class_exists($definition)) {
            return new $definition();
        }

        if (is_callable($definition)) {
            return $definition($this);
        }

        if (class_exists($key) && is_subclass_of($key, Module::class)) {
            $this->singleton($key, function () use ($key) {
                return new $key($this->get(Loader::class));
            });

            return $this->get($key);
        }

        if (class_exists($key)) {
            return new $key();
        }

        if (!$this->has($key)) {
            throw new Exception("Key ${key} not found.");
        }

        return $definition;
    }

    public function has(string $key): bool
    {
        return isset($this->definitions[$key]);
    }

    public function set(string $key, $value): void
    {
        $this->definitions[$key] = $value;
    }

    public function singleton($key, $value): void
    {
        if (isset($this->singletons[$key])) {
            throw new Exception('Singleton already exists.');
        }

        $this->singletons[] = $key;
        $this->definitions[$key] = $value;
    }
}