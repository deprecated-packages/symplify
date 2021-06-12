<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Json\JsonFileSystem;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\FileSystem\PathResolver;

/**
 * @see \Symplify\VendorPatches\Tests\Composer\PackageNameResolverTest
 */
final class PackageNameResolver
{
    public function __construct(
        private JsonFileSystem $jsonFileSystem,
        private PathResolver $pathResolver,
        private FileSystemGuard $fileSystemGuard
    ) {
    }

    public function resolveFromFileInfo(SmartFileInfo $vendorFile): string
    {
        $packageComposerJsonFilePath = $this->getPackageComposerJsonFilePath($vendorFile);

        $composerJson = $this->jsonFileSystem->loadFilePathToJson($packageComposerJsonFilePath);
        if (! isset($composerJson['name'])) {
            throw new ShouldNotHappenException();
        }

        return $composerJson['name'];
    }

    private function getPackageComposerJsonFilePath(SmartFileInfo $vendorFileInfo): string
    {
        $vendorPackageDirectory = $this->pathResolver->resolveVendorDirectory($vendorFileInfo);
        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        $this->fileSystemGuard->ensureFileExists($packageComposerJsonFilePath, __METHOD__);

        return $packageComposerJsonFilePath;
    }
}
