<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Tests\Regex\NonExistingClassConstantExtractor;

use Iterator;
use Symplify\ClassPresence\HttpKernel\ClassPresenceKernel;
use Symplify\ClassPresence\Regex\NonExistingClassConstantExtractor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassConstantExtractorTest extends AbstractKernelTestCase
{
    /**
     * @var NonExistingClassConstantExtractor
     */
    private $nonExistingClassConstantExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(ClassPresenceKernel::class);
        $this->nonExistingClassConstantExtractor = self::$container->get(NonExistingClassConstantExtractor::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedMissingCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $nonExistingClassConstants = $this->nonExistingClassConstantExtractor->extractFromFileInfo($fileInfo);
        $this->assertCount($expectedMissingCount, $nonExistingClassConstants);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/template/non_existing.latte', 1];
        yield [__DIR__ . '/Fixture/template/non_existing.twig', 1];

        yield [__DIR__ . '/Fixture/template/existing_with_number.latte', 0];
        yield [__DIR__ . '/Fixture/template/existing_with_lowercase.latte', 0];
        yield [__DIR__ . '/Fixture/template/existing.latte', 0];
        yield [__DIR__ . '/Fixture/template/existing.twig', 0];
    }
}
