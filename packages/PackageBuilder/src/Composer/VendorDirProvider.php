<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Composer;

use Composer\Autoload\ClassLoader;
use Nette\Utils\Strings;
use ReflectionClass;

final class VendorDirProvider
{
    public static function provide(): string
    {
        $rootFolder = getenv('SystemDrive', true) . DIRECTORY_SEPARATOR;

        $path = __DIR__;
        while (! Strings::endsWith($path, 'vendor') && $path !== $rootFolder) {
            $path = dirname($path);
        }

        if ($path !== $rootFolder) {
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
