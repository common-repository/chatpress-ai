<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInita9eb96bfda2095cfc5c18b15429a356e
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

        spl_autoload_register(array('ComposerAutoloaderInita9eb96bfda2095cfc5c18b15429a356e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInita9eb96bfda2095cfc5c18b15429a356e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInita9eb96bfda2095cfc5c18b15429a356e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
