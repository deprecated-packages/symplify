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
        ?bool $symlink
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
            ];
            if ($symlink !== null) {
                $repositoriesContent['options'] = [
                    'symlink' => $symlink,
                ];
            }

            if (array_key_exists(ComposerJsonSection::REPOSITORIES, $packageComposerJson)) {
                array_unshift($packageComposerJson[ComposerJsonSection::REPOSITORIES], $repositoriesContent);
            } else {
                $packageComposerJson[ComposerJsonSection::REPOSITORIES][] = $repositoriesContent;
            }
        }

        return $packageComposerJson;
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $packageNames
     * @return mixed[]
     */
    public function removePackageSymlinksFromPackageComposerJson(
        array $packageComposerJson,
        array $packageNames,
        SmartFileInfo $mainComposerJsonFileInfo
    ): array {
        // If there are no repositories, then nothing to do
        if (! array_key_exists(ComposerJsonSection::REPOSITORIES, $packageComposerJson)) {
            return $packageComposerJson;
        }
        foreach ($packageNames as $packageName) {
            $usedPackageFileInfo = $this->composerJsonProvider->getPackageFileInfoByName($packageName);

            $relativePathToLocalPackage = $this->packagePathResolver->resolveRelativePathToLocalPackage(
                $mainComposerJsonFileInfo,
                $usedPackageFileInfo
            );

            // Filter out the repositories of type "path" with this URL
            $packageComposerJson[ComposerJsonSection::REPOSITORIES] = array_values(array_filter(
                $packageComposerJson[ComposerJsonSection::REPOSITORIES],
                function (array $repository) use ($relativePathToLocalPackage): bool {
                    return ! (
                        isset($repository['type']) && $repository['type'] === 'path'
                        && isset($repository['url']) && $repository['url'] === $relativePathToLocalPackage
                    );
                }
            ));
        }

        return $packageComposerJson;
    }
}
