<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit44fe795794ce8c0e42d1a1c62933879e
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
        'G' => 
        array (
            'GatewayWorker\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman-for-win',
        ),
        'GatewayWorker\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/gateway-worker-for-win/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit44fe795794ce8c0e42d1a1c62933879e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit44fe795794ce8c0e42d1a1c62933879e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
