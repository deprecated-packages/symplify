<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\PathResolver;

use Nette\Utils\Strings;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Merge\PathResolver\AutoloadPathNormalizerTest
 */
final class AutoloadPathNormalizer
{
    /**
     * @var string[]
     */
    private const SECTIONS_WITH_PATH = ['classmap', 'files', 'exclude-from-classmap', 'psr-4', 'psr-0'];

    /**
     * Class map path needs to be prefixed before merge, otherwise will override one another
     *
     * @see https://github.com/symplify/symplify/issues/1333
     */
    public function normalizeAutoloadPaths(ComposerJson $packageComposerJson, SmartFileInfo $packageFile): void
    {
        $autoload = $this->normalizeAutoloadArray($packageFile, $packageComposerJson->getAutoload());
        $packageComposerJson->setAutoload($autoload);

        $autoloadDev = $this->normalizeAutoloadArray($packageFile, $packageComposerJson->getAutoloadDev());
        $packageComposerJson->setAutoloadDev($autoloadDev);
    }

    /**
     * @return mixed[]
     */
    private function normalizeAutoloadArray(SmartFileInfo $packageFile, array $autoloadArray): array
    {
        foreach (self::SECTIONS_WITH_PATH as $sectionWithPath) {
            if (! isset($autoloadArray[$sectionWithPath])) {
                continue;
            }

            $autoloadArray[$sectionWithPath] = $this->relativizePath($autoloadArray[$sectionWithPath], $packageFile);
        }

        return $autoloadArray;
    }

    /**
     * @param mixed[] $autoloadSubsection
     * @return mixed[]
     */
    private function relativizePath(array $autoloadSubsection, SmartFileInfo $packageFileInfo): array
    {
        $packageRelativeDirectory = dirname($packageFileInfo->getRelativeFilePathFromDirectory(getcwd()));

        foreach ($autoloadSubsection as $key => $value) {
            if (is_array($value)) {
                $autoloadSubsection[$key] = array_map(function ($path) use ($packageRelativeDirectory): string {
                    return $this->relativizeSinglePath($packageRelativeDirectory, $path);
                }, $value);
            } else {
                $autoloadSubsection[$key] = $this->relativizeSinglePath($packageRelativeDirectory, $value);
            }
        }

        return $autoloadSubsection;
    }

    private function relativizeSinglePath(string $packageRelativeDirectory, string $path): string
    {
        // prevent prefixing, as vendor is the same in both locations
        if (Strings::startsWith($path, 'vendor/')) {
            return $path;
        }

        return $packageRelativeDirectory . '/' . ltrim($path, '/');
    }
}
