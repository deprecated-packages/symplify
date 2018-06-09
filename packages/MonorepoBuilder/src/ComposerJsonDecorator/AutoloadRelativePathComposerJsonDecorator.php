<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
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
            if (! in_array($key, ['autoload', 'autoload-dev'], true)) {
                continue;
            }

            foreach ($values as $autoloadType => $autoloadPaths) {
                if ($autoloadType !== 'psr-4') {
                    continue;
                }

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
        foreach ($autoloadPaths as $namespace => $path) {
            foreach ($packageComposerFiles as $packageComposerFile) {
                $namespaceWithSlashes = addslashes($namespace);
                if (! Strings::contains($packageComposerFile->getContents(), $namespaceWithSlashes)) {
                    continue;
                }

                $relativeDirectory = substr($packageComposerFile->getPath(), strlen(getcwd()) + 1);
                $path = $relativeDirectory . DIRECTORY_SEPARATOR . $path;

                $composerJson[$key][$autoloadType][$namespace] = $path;
            }
        }
        return $composerJson;
    }
}
