<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\ActiveClass\ClassNameResolver;

use Iterator;
use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Tests\ActiveClass\ClassNameResolver\Fixture\SomeClass;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassNameResolverTest extends AbstractKernelTestCase
{
    private ClassNameResolver $classNameResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->classNameResolver = $this->getService(ClassNameResolver::class);
    }

    /**
     * @dataProvider provideData()
     * @param class-string $expectedClassName
     */
    public function test(SmartFileInfo $fileInfo, string $expectedClassName): void
    {
        $resolvedClassName = $this->classNameResolver->resolveFromFromFileInfo($fileInfo);
        $this->assertSame($expectedClassName, $resolvedClassName);
    }

    /**
     * @return Iterator<array<class-string<SomeClass>>|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/SomeClass.php'), SomeClass::class];
    }
}
