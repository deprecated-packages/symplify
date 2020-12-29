<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\ComposerJson;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\PathResolver\PackagePathResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Testing\Tests\ComposerJson\ComposerJsonSymlinkerTest
 */
final class ComposerJsonSymlinker
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var PackagePathResolver
     */
    private $packagePathResolver;

    public function __construct(ComposerJsonProvider $composerJsonProvider, PackagePathResolver $packagePathResolver)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packagePathResolver = $packagePathResolver;
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $packageNames
     * @return mixed[]
     */
    public function decoratePackageComposerJsonWithPackageSymlinks(
        array $packageComposerJson,
        array $packageNames,
        SmartFileInfo $mainComposerJsonFileInfo,
        bool $symlink
    ): array {
        // @see https://getcomposer.org/doc/05-repositories.md#path
        foreach ($packageNames as $packageName) {
            $usedPackageFileInfo = $this->composerJsonProvider->getPackageFileInfoByName($packageName);

            $relativePathToLocalPackage = $this->packagePathResolver->resolveRelativePathToLocalPackage(
                $mainComposerJsonFileInfo,
                $usedPackageFileInfo
            );

            $repositoriesContent = [
                'type' => 'path',
                'url' => $relativePathToLocalPackage,
                'options' => [
                    'symlink' => $symlink,
                ],
            ];

            if (array_key_exists(ComposerJsonSection::REPOSITORIES, $packageComposerJson)) {
                $packageComposerJson = $this->addRepositoryEntryToPackageComposerJson(
                    $packageComposerJson,
                    $repositoriesContent
                );
            } else {
                $packageComposerJson[ComposerJsonSection::REPOSITORIES][] = $repositoriesContent;
            }
        }

        return $packageComposerJson;
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param mixed[] $repositoriesContent
     * @return mixed[]
     */
    private function addRepositoryEntryToPackageComposerJson(
        array $packageComposerJson,
        array $repositoriesContent
    ): array {
        // First check if this entry already exists and, if so, replace it
        foreach ($packageComposerJson[ComposerJsonSection::REPOSITORIES] as $key => $repository) {
            if ($this->isSamePackageEntry($repository, $repositoriesContent)) {
                // Just override the "options"
                if (isset($repositoriesContent['options'])) {
                    $packageComposerJson[ComposerJsonSection::REPOSITORIES][$key]['options'] = $repositoriesContent['options'];
                } else {
                    unset($packageComposerJson[ComposerJsonSection::REPOSITORIES][$key]['options']);
                }
                return $packageComposerJson;
            }
        }
        // Add the new entry
        array_unshift($packageComposerJson[ComposerJsonSection::REPOSITORIES], $repositoriesContent);
        return $packageComposerJson;
    }

    /**
     * @param mixed[] $repository
     * @param mixed[] $repositoriesContent
     */
    private function isSamePackageEntry(array $repository, array $repositoriesContent): bool
    {
        return isset($repository['type']) && $repository['type'] === $repositoriesContent['type']
            && isset($repository['url']) && $repository['url'] === $repositoriesContent['url'];
    }
}
