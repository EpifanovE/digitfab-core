<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit223a19c62776f674fb6bbe0c5fe10194
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit223a19c62776f674fb6bbe0c5fe10194', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit223a19c62776f674fb6bbe0c5fe10194', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit223a19c62776f674fb6bbe0c5fe10194::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
