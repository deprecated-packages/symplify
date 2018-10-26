<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Composer;

use Composer\Autoload\ClassLoader;
use Nette\Utils\Strings;
use ReflectionClass;

final class VendorDirProvider
{
    public static function provide(): string
    {
        $path = __DIR__;
        while (! Strings::endsWith($path, 'vendor') && $path !== '/') {
            $path = dirname($path);
        }

        if ($path !== '/') {
            return $path;
        }

        return self::reflectionFallback();
    }

    private static function reflectionFallback(): string
    {
        $classLoaderReflection = new ReflectionClass(ClassLoader::class);

        return dirname(dirname($classLoaderReflection->getFileName()));
    }
}
