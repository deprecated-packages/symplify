<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\ContentAnalyzer\NonExistingClassExtractor;

use Iterator;
use Symplify\EasyCI\ContentAnalyzer\NonExistingClassExtractor;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var NonExistingClassExtractor
     */
    private $nonExistingClassExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->nonExistingClassExtractor = $this->getService(NonExistingClassExtractor::class);

        require_once __DIR__ . '/Source/LowercaseFactory.php';
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedClassCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $nonExistingClasses = $this->nonExistingClassExtractor->extractFromFileInfo($fileInfo);
        $this->assertCount($expectedClassCount, $nonExistingClasses);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/config/skip_psr4_autodiscovery.yaml', 0];
        yield [__DIR__ . '/Fixture/config/nette/skip_routing_mapping.neon', 1];

        // nette
        yield [__DIR__ . '/Fixture/config/mapping_only.neon', 0];
        yield [__DIR__ . '/Fixture/config/some_config.neon', 1];
        yield [__DIR__ . '/Fixture/config/static_call.neon', 1];
        yield [__DIR__ . '/Fixture/config/factory_lowercase.neon', 0];
        yield [__DIR__ . '/Fixture/config/class_underscore.neon', 0];

        // templates
        yield [__DIR__ . '/Fixture/template/file.latte', 2];
        yield [__DIR__ . '/Fixture/template/file_with_existing_class.latte', 0];
        yield [__DIR__ . '/Fixture/template/file_with_existing_class.twig', 0];
        yield [__DIR__ . '/Fixture/template/different_file.twig', 1];

        // blade, laravel
        yield [__DIR__ . '/Fixture/template/non_existing_in_blade_file.php', 3];
        yield [__DIR__ . '/Fixture/template/existing_in_blade_file.php', 0];
    }
}
