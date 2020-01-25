<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Tests\PathResolver;

use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Testing\PathResolver\PackagePathResolver;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackagePathResolverTest extends AbstractKernelTestCase
{
    /**
     * @var PackagePathResolver
     */
    private $packagePathResolver;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);
        $this->packagePathResolver = self::$container->get(PackagePathResolver::class);
    }

    public function test(): void
    {
        $rootComposerJson = new SmartFileInfo(__DIR__ . '/PackagePathResolverTestSource/some_root/composer.json');

        $packageComposerJson = new SmartFileInfo(
            __DIR__ . '/PackagePathResolverTestSource/some_root/nested_packages/nested/composer.json'
        );

        $relativePathToLocalPackage = $this->packagePathResolver->resolveRelativePathToLocalPackage(
            $rootComposerJson,
            $packageComposerJson
        );

        $this->assertSame('../../nested_packages/nested', $relativePathToLocalPackage);
    }
}
