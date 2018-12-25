<?php declare(strict_types=1);

namespace Symplify\Util\Composer;

use Composer\Package\RootPackageInterface;
use Composer\Script\Event;
use Nette\Utils\Strings;

final class PackagePsr4Autoloader
{
    /**
     * @var string
     */
    private const MAIN_NAMESPACE = 'Symplify';

    /**
     * @var string
     */
    private const PACKAGES = 'packages';

    /**
     * @see https://www.drupal.org/files/issues/vendor-classmap-2468499-14.patch
     */
    public static function autoload(Event $event): void
    {
        $absolutePackagesDirectory = getcwd() . '/' . self::PACKAGES . '/*';
        $package = $event->getComposer()->getPackage();

        self::autoloadSrc($absolutePackagesDirectory, $package);
        self::autoloadTests($absolutePackagesDirectory, $package);
    }

    /**
     * @return string[]
     */
    private static function getDirectoriesInPath(string $directory, string $name): array
    {
        $globResult = self::globRecursive($directory, GLOB_ONLYDIR);

        $directories = array_filter($globResult, function ($path) use ($name) {
            // keep only dirs ending with "$name"
            return Strings::match($path, '#\/' . preg_quote($name) . '$#') && ! Strings::contains($path, 'templates');
        });

        $directories = self::absolutizePaths($directories);

        return self::relativizeToCwd($directories);
    }

    /**
     * @see https://stackoverflow.com/a/12109100/1348344
     * @return string[]
     */
    private static function globRecursive(string $pattern, int $flags = 0): array
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::globRecursive($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }

    /**
     * @param string[] $directories
     * @return string[]
     */
    private static function relativizeToCwd(array $directories): array
    {
        foreach ($directories as $key => $directory) {
            $directories[$key] = Strings::substring($directory, strlen(getcwd()) + 1);
        }

        return $directories;
    }

    /**
     * @param string[] $paths
     * @return string[]
     */
    private static function absolutizePaths(array $paths): array
    {
        foreach ($paths as $key => $path) {
            $realpath = realpath($path);
            if ($realpath) {
                $paths[$key] = $realpath;
            }
        }

        return $paths;
    }

    /**
     * From:
     * "packages/SomePath/src"
     *
     * to:
     * "Rector\SomePath\" => "packages/SomePath/src"
     *
     * @param string[] $directories
     * @return string[]
     */
    private static function createNamespaceToDirectory(
        array $directories,
        string $packagesDirectory,
        string $namespaceSuffix = ''
    ): array {
        $namespaceToDirectory = [];
        foreach ($directories as $directory) {
            $pattern = '#' . $packagesDirectory . '/(?<namespacePart>.*?)/(src|tests|packages)(/(?<secondNamespacePart>.*?)/)?#';

            $match = Strings::match($directory, $pattern);
            if (! isset($match['namespacePart'])) {
                continue;
            }

            $namespace = self::MAIN_NAMESPACE . '\\' . $match['namespacePart'] . '\\';

            // subpackage
            if (isset($match['secondNamespacePart'])) {
                $namespace .= $match['secondNamespacePart'] . '\\';
            }

            if ($namespaceSuffix) {
                $namespace .= $namespaceSuffix . '\\';
            }

            $namespaceToDirectory[$namespace] = $directory;
        }

        return $namespaceToDirectory;
    }

    private static function autoloadSrc(string $absolutePackagesDirectory, RootPackageInterface $package): void
    {
        $srcDirectories = self::getDirectoriesInPath($absolutePackagesDirectory, 'src');
        $namespaceToDirectory = self::createNamespaceToDirectory($srcDirectories, self::PACKAGES);

        $autoload = $package->getAutoload();
        $autoload['psr-4'] = array_merge($autoload['psr-4'] ?? [], $namespaceToDirectory);
        $package->setAutoload($autoload);
    }

    private static function autoloadTests(string $absolutePackagesDirectory, RootPackageInterface $package): void
    {
        $testDirectories = self::getDirectoriesInPath($absolutePackagesDirectory, 'tests');
        $namespaceToDirectory = self::createNamespaceToDirectory(
            $testDirectories,
            self::PACKAGES,
            'Tests'
        );

        $devAutoload = $package->getDevAutoload();
        $devAutoload['psr-4'] = array_merge($devAutoload['psr-4'] ?? [], $namespaceToDirectory);
        $package->setDevAutoload($devAutoload);
    }
}
