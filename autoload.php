<?php

$autoload = new Autoload();
$autoload::load();

class Autoload
{
    const APP = 'app';
    const SRC = 'src';

    public static function load()
    {
        spl_autoload_register('self::loaderFn');
    }

    protected static function loaderFn($class)
    {
        $class = str_replace('\\', '/', $class);

        if (file_exists($file = self::APP . '/' . $class . '.php')) {
            include_once $file;
        }

        if (file_exists($file = self::SRC . '/' . $class . '.php')) {
            include_once $file;
        }
    }
}