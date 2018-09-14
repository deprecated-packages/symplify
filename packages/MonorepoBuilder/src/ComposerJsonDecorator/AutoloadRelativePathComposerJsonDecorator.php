<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use function Safe\getcwd;

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
        $composerJson = $this->processPsr4(
            $composerJson,
            $packageComposerFiles,
            $key,
            $autoloadType,
            $autoloadPaths
        );

        return $this->processFiles($composerJson, $packageComposerFiles, $key, $autoloadType, $autoloadPaths);
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
        if (! in_array($autoloadType, ['psr-0', 'psr-4'], true)) {
            return $composerJson;
        }

        foreach ($autoloadPaths as $namespace => $path) {
            foreach ($packageComposerFiles as $packageComposerFile) {
                $namespaceWithSlashes = addslashes($namespace);

                if (! Strings::contains($packageComposerFile->getContents(), '"' . $namespaceWithSlashes . '"')) {
                    continue;
                }

                $composerJson[$key][$autoloadType][$namespace] = $this->prefixPath($packageComposerFile, $path);
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
        if (! in_array($autoloadType, ['files', 'classmap'], true)) {
            return $composerJson;
        }

        foreach ($files as $i => $file) {
            foreach ($packageComposerFiles as $packageComposerFile) {
                if (! Strings::contains($packageComposerFile->getContents(), $file)) {
                    continue;
                }

                $composerJson[$key][$autoloadType][$i] = $this->prefixPath($packageComposerFile, $file);
            }
        }

        return $composerJson;
    }

    /**
     * @param string[]|string $path
     * @return string[]|string
     */
    private function prefixPath(SplFileInfo $packageComposerFile, $path)
    {
        if (is_array($path)) {
            foreach ($path as $i => $singlePath) {
                $path[$i] = $this->prefixPath($packageComposerFile, $singlePath);
            }

            return $path;
        }

        $composerDirectory = dirname($packageComposerFile->getRealPath());
        $relativeDirectory = Strings::substring($composerDirectory, strlen(rtrim(getcwd(), '/') . '/'));

        return $relativeDirectory . DIRECTORY_SEPARATOR . $path;
    }
}
