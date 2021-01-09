<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Composer;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\VendorPatches\Composer\PackageNameResolver;
use Symplify\VendorPatches\HttpKernel\VendorPatchesKernel;

final class PackageNameResolverTest extends AbstractKernelTestCase
{
    /**
     * @var PackageNameResolver
     */
    private $packageNameResolver;

    protected function setUp(): void
    {
        $this->bootKernel(VendorPatchesKernel::class);

        $this->packageNameResolver = $this->getService(PackageNameResolver::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/PackageNameResolverSource/vendor/some/pac.kage/composer.json');

        $packageName = $this->packageNameResolver->resolveFromFileInfo($fileInfo);
        $this->assertSame('some/name', $packageName);
    }
}
