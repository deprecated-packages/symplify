<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Config\ClassExtractor;

use Iterator;
use stdClass;
use Symplify\EasyCI\Config\ClassExtractor;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassExtractorTest extends AbstractKernelTestCase
{
    private ClassExtractor $classExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->classExtractor = $this->getService(ClassExtractor::class);
    }

    /**
     * @dataProvider provideData()
     * @param string[] $expectedClasses
     */
    public function test(string $filePath, array $expectedClasses): void
    {
        $fileInfo = new SmartFileInfo($filePath);
        $extractedClasses = $this->classExtractor->extractFromFileInfo($fileInfo);

        $this->assertSame($expectedClasses, $extractedClasses);
    }

    /**
     * @return Iterator<string[]|array<string[]>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/some_file.neon', [stdClass::class]];
        yield [__DIR__ . '/Fixture/static_call.neon', ['App\Utils\CustomMacros']];
        yield [__DIR__ . '/Fixture/list_services.neon', ['App\Some\Service']];
    }
}
