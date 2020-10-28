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
        SmartFileInfo $mainComposerJsonFileInfo
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
                // we need hard copy of files, as in normal composer install of standalone package
                'options' => [
                    'symlink' => false,
                ],

                // since composer 2.0 - see https://getcomposer.org/doc/articles/repository-priorities.md#default-behavior
                // 'canonical' => true,
            ];

            if (array_key_exists(ComposerJsonSection::REPOSITORIES, $packageComposerJson)) {
                array_unshift($packageComposerJson[ComposerJsonSection::REPOSITORIES], $repositoriesContent);
            } else {
                $packageComposerJson[ComposerJsonSection::REPOSITORIES][] = $repositoriesContent;
            }
        }

        // https://getcomposer.org/doc/articles/repository-priorities.md#filtering-packages - do not required 3rd party "<your_vendor>/*" packages from packagist - use local version with priority
//        $packageComposerJson[ComposerJsonSection::REPOSITORIES][] = [
//            'type' => 'composer',
//            'url' => 'https://packagist.org',
//            'exclude' => $packageNames,
//        ];

        return $packageComposerJson;
    }
}
