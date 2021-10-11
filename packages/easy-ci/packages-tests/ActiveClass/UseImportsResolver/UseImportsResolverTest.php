<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver;

use Iterator;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\Source\FirstUsedClass;
use Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\Source\SecondUsedClass;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class UseImportsResolverTest extends AbstractKernelTestCase
{
    private UseImportsResolver $useImportsResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->useImportsResolver = $this->getService(UseImportsResolver::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $fileInfos, array $expectedClassUsages): void
    {
        $resolvedClassUsages = $this->useImportsResolver->resolveFromFileInfos($fileInfos);
        $this->assertSame($expectedClassUsages, $resolvedClassUsages);
    }

    public function provideData(): Iterator
    {
        $fileInfos = [new SmartFileInfo(__DIR__ . '/Fixture/FileUsingOtherClasses.php')];

        yield [$fileInfos, [FirstUsedClass::class, SecondUsedClass::class]];
    }
}
