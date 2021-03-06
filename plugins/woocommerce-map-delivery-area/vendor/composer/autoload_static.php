<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf3bcbd0a2dd259c5a05753fbd080efc1
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Location\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Location\\' => 
        array (
            0 => __DIR__ . '/..' . '/mjaschen/phpgeo/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf3bcbd0a2dd259c5a05753fbd080efc1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf3bcbd0a2dd259c5a05753fbd080efc1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf3bcbd0a2dd259c5a05753fbd080efc1::$classMap;

        }, null, ClassLoader::class);
    }
}
