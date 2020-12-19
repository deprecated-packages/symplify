<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Json\JsonFileSystem;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\FileSystem\PathResolver;

final class PackageNameResolver
{
    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    /**
     * @var PathResolver
     */
    private $pathResolver;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    public function __construct(
        JsonFileSystem $jsonFileSystem,
        PathResolver $pathResolver,
        FileSystemGuard $fileSystemGuard
    ) {
        $this->jsonFileSystem = $jsonFileSystem;
        $this->pathResolver = $pathResolver;
        $this->fileSystemGuard = $fileSystemGuard;
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
