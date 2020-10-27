<?php

declare(strict_types=1);

namespace Symplify\Skipper\Tests\Skipper\Only;

use Iterator;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Skipper\HttpKernel\SkipperKernel;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SkipperOnlyTest extends AbstractKernelTestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(SkipperKernel::class, [__DIR__ . '/config.php']);

        $this->skipper = self::$container->get(Skipper::class);
    }

    /**
     * @dataProvider provideCheckerAndFile()
     */
    public function testCheckerAndFile(string $class, string $filePath, bool $expected): void
    {
        $resolvedSkip = $this->skipper->shouldSkipElementAndFileInfo($class, new SmartFileInfo($filePath));
        $this->assertSame($expected, $resolvedSkip);
    }

    public function provideCheckerAndFile(): Iterator
    {
        yield [LineLengthFixer::class, __DIR__ . '/Source/SomeFileToOnlyInclude.php', false];
        yield [LineLengthFixer::class, __DIR__ . '/Source/SomeFile.php', true];

        // no restrictions
        yield [ArraySyntaxFixer::class, __DIR__ . '/Source/SomeFileToOnlyInclude.php', false];
        yield [ArraySyntaxFixer::class, __DIR__ . '/Source/SomeFile.php', false];
    }
}
