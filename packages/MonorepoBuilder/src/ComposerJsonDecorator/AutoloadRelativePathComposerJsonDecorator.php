<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\PackageComposerFinder;

final class AutoloadRelativePathComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var PackageComposerFinder
     */
    private $packageComposerFinder;

    public function __construct(PackageComposerFinder $packageComposerFinder)
    {
        $this->packageComposerFinder = $packageComposerFinder;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        $packageComposerFiles = $this->packageComposerFinder->getPackageComposerFiles();

        foreach ($composerJson as $key => $values) {
            if (! in_array($key, [Section::AUTOLOAD, Section::AUTOLOAD_DEV], true)) {
                continue;
            }

            foreach ($values as $autoloadType => $autoloadPaths) {
                $composerJson = $this->processAutoloadPaths(
                    $composerJson,
                    $autoloadPaths,
                    $packageComposerFiles,
                    $key,
                    $autoloadType
                );
            }
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     * @param string[] $autoloadPaths
     * @param SplFileInfo[] $packageComposerFiles
     * @return mixed[]
     */
    private function processAutoloadPaths(
        array $composerJson,
        array $autoloadPaths,
        array $packageComposerFiles,
        string $key,
        string $autoloadType
    ): array {
        if (in_array($autoloadType, ['psr-0', 'psr-4'], true)) {
            $composerJson = $this->processPsr4(
                $composerJson,
                $packageComposerFiles,
                $key,
                $autoloadType,
                $autoloadPaths
            );
        }

        if (in_array($autoloadType, ['files', 'classmap'], true)) {
            $composerJson = $this->processFiles(
                $composerJson,
                $packageComposerFiles,
                $key,
                $autoloadType,
                $autoloadPaths
            );
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     * @param mixed[] $packageComposerFiles
     * @param mixed[] $autoloadPaths
     * @return mixed[]
     */
    private function processPsr4(
        array $composerJson,
        array $packageComposerFiles,
        string $key,
        string $autoloadType,
        array $autoloadPaths
    ): array {
        foreach ($autoloadPaths as $namespace => $path) {
            foreach ($packageComposerFiles as $packageComposerFile) {
                $namespaceWithSlashes = addslashes($namespace);

                if (! Strings::contains($packageComposerFile->getContents(), $namespaceWithSlashes)) {
                    continue;
                }

                $composerDirectory = dirname($packageComposerFile->getRealPath());
                $relativeDirectory = substr($composerDirectory, strlen(getcwd()) + 1);

                $path = $relativeDirectory . DIRECTORY_SEPARATOR . $path;

                $composerJson[$key][$autoloadType][$namespace] = $path;
            }
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     * @param mixed[] $packageComposerFiles
     * @param string[] $files
     * @return mixed[]
     */
    private function processFiles(
        array $composerJson,
        array $packageComposerFiles,
        string $key,
        string $autoloadType,
        array $files
    ): array {
        foreach ($files as $fileKey => $file) {
            foreach ($packageComposerFiles as $packageComposerFile) {
                if (! Strings::contains($packageComposerFile->getContents(), $file)) {
                    continue;
                }

                $composerDirectory = dirname($packageComposerFile->getRealPath());
                $relativeDirectory = substr($composerDirectory, strlen(getcwd()) + 1);

                $composerJson[$key][$autoloadType][$fileKey] = $relativeDirectory . DIRECTORY_SEPARATOR . $file;
            }
        }

        return $composerJson;
    }
}
