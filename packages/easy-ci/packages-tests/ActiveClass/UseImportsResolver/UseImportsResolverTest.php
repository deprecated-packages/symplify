<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver;

use Iterator;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\Kernel\EasyCIKernel;
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
     * @param SmartFileInfo[] $fileInfos
     * @param class-string<FirstUsedClass>[]|class-string<SecondUsedClass>[] $expectedClassUsages
     */
    public function test(array $fileInfos, array $expectedClassUsages): void
    {
        $resolvedClassUsages = $this->useImportsResolver->resolveFromFileInfos($fileInfos);
        $this->assertSame($expectedClassUsages, $resolvedClassUsages);
    }

    /**
     * @return Iterator<array<int, array<class-string<FirstUsedClass>|SmartFileInfo>>|array<int, array<class-string<FirstUsedClass>|class-string<SecondUsedClass>|SmartFileInfo>>>
     */
    public function provideData(): Iterator
    {
        $fileInfos = [new SmartFileInfo(__DIR__ . '/Fixture/FileUsingOtherClasses.php')];

        yield [$fileInfos, [FirstUsedClass::class, SecondUsedClass::class]];
    }
}
