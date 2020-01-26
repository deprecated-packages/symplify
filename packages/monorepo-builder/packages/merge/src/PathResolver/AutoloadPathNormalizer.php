<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\PathResolver;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AutoloadPathNormalizer
{
    /**
     * @var string[]
     */
    private const SECTIONS_WITH_PATH = ['classmap', 'files', 'exclude-from-classmap', 'psr-4', 'psr-0'];

    /**
     * Class map path needs to be prefixed before merge, otherwise will override one another
     * @see https://github.com/symplify/symplify/issues/1333
     */
    public function normalizeAutoloadPaths(ComposerJson $packageComposerJson, SmartFileInfo $packageFile): void
    {
        $this->normalizeAutoload($packageComposerJson, $packageFile);
        $this->normalizeAutoloadDev($packageComposerJson, $packageFile);
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
                    return $packageRelativeDirectory . '/' . ltrim($path, '/');
                }, $value);
            } else {
                $autoloadSubsection[$key] = $packageRelativeDirectory . '/' . ltrim($value, '/');
            }
        }

        return $autoloadSubsection;
    }

    private function normalizeAutoload(ComposerJson $packageComposerJson, SmartFileInfo $packageFile): void
    {
        $autoload = $packageComposerJson->getAutoload();
        foreach (self::SECTIONS_WITH_PATH as $sectionWithPath) {
            if (! isset($autoload[$sectionWithPath])) {
                continue;
            }

            // @todo objectivize?
            $autoload[$sectionWithPath] = $this->relativizePath($autoload[$sectionWithPath], $packageFile);
        }

        $packageComposerJson->setAutoload($autoload);
    }

    private function normalizeAutoloadDev(ComposerJson $packageComposerJson, SmartFileInfo $packageFile): void
    {
        $autoloadDev = $packageComposerJson->getAutoloadDev();
        foreach (self::SECTIONS_WITH_PATH as $sectionWithPath) {
            if (! isset($autoloadDev[$sectionWithPath])) {
                continue;
            }

            // @todo objectivize?
            $autoloadDev[$sectionWithPath] = $this->relativizePath($autoloadDev[$sectionWithPath], $packageFile);
        }

        $packageComposerJson->setAutoloadDev($autoloadDev);
    }
}
