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
     * @var string
     */
    private const TYPE = 'type';

    /**
     * @var string
     */
    private const URL = 'url';

    /**
     * @var string
     */
    private const OPTIONS = 'options';

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
                self::TYPE => 'path',
                self::URL => $relativePathToLocalPackage,
                self::OPTIONS => [
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
                if (isset($repositoriesContent[self::OPTIONS])) {
                    $packageComposerJson[ComposerJsonSection::REPOSITORIES][$key][self::OPTIONS] = $repositoriesContent[self::OPTIONS];
                } else {
                    unset($packageComposerJson[ComposerJsonSection::REPOSITORIES][$key][self::OPTIONS]);
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
        if (! isset($repository[self::TYPE])) {
            return false;
        }
        if ($repository[self::TYPE] !== $repositoriesContent[self::TYPE]) {
            return false;
        }
        if (! isset($repository[self::URL])) {
            return false;
        }
        return $repository[self::URL] === $repositoriesContent[self::URL];
    }
}
