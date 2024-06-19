<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite36e141f78c9753cf3ad2d9df4e1f734
{
    public static $files = array (
        '941748b3c8cae4466c827dfb5ca9602a' => __DIR__ . '/..' . '/rmccue/requests/library/Deprecated.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WpOrg\\Requests\\' => 15,
        ),
        'E' => 
        array (
            'Epayco\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WpOrg\\Requests\\' => 
        array (
            0 => __DIR__ . '/..' . '/rmccue/requests/src',
        ),
        'Epayco\\' => 
        array (
            0 => __DIR__ . '/..' . '/epayco/epayco-php/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Requests' => __DIR__ . '/..' . '/rmccue/requests/library/Requests.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite36e141f78c9753cf3ad2d9df4e1f734::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite36e141f78c9753cf3ad2d9df4e1f734::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite36e141f78c9753cf3ad2d9df4e1f734::$classMap;

        }, null, ClassLoader::class);
    }
}
