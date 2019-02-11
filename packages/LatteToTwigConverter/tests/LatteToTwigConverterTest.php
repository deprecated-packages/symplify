<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Tests;

use Iterator;
use Symplify\LatteToTwigConverter\HttpKernel\LatteToTwigConverterKernel;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class LatteToTwigConverterTest extends AbstractKernelTestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    protected function setUp(): void
    {
        $this->bootKernel(LatteToTwigConverterKernel::class);

        $this->latteToTwigConverter = self::$container->get(LatteToTwigConverter::class);
    }

    /**
     * @dataProvider provideData()
     * @dataProvider provideDataForNMacros()
     * @dataProvider provideDataForFilters()
     */
    public function test(string $latteFile, string $expectedTwigFile): void
    {
        $fileInfo = new SmartFileInfo($latteFile);
        $convertedFile = $this->latteToTwigConverter->convertFile($fileInfo);
        $this->assertStringEqualsFile($expectedTwigFile, $convertedFile, 'Caused in file: ' . $latteFile);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/latte/variables.latte', __DIR__ . '/Source/twig/variables.twig'];
        yield [__DIR__ . '/Source/latte/block-file.latte', __DIR__ . '/Source/twig/block-file.twig'];
        yield [__DIR__ . '/Source/latte/loops.latte', __DIR__ . '/Source/twig/loops.twig'];
        yield [__DIR__ . '/Source/latte/conditions.latte', __DIR__ . '/Source/twig/conditions.twig'];
        yield [__DIR__ . '/Source/latte/comment.latte', __DIR__ . '/Source/twig/comment.twig'];
        yield [__DIR__ . '/Source/latte/capture.latte', __DIR__ . '/Source/twig/capture.twig'];
        yield [__DIR__ . '/Source/latte/javascript.latte', __DIR__ . '/Source/twig/javascript.twig'];

        yield [__DIR__ . '/Source/latte/extends.latte', __DIR__ . '/Source/twig/extends.twig'];
        yield [__DIR__ . '/Source/latte/default.latte', __DIR__ . '/Source/twig/default.twig'];
        yield [__DIR__ . '/Source/latte/nested_variable.latte', __DIR__ . '/Source/twig/nested_variable.twig'];
        yield [__DIR__ . '/Source/latte/first_last.latte', __DIR__ . '/Source/twig/first_last.twig'];
        yield [__DIR__ . '/Source/latte/include.latte', __DIR__ . '/Source/twig/include.twig'];
        yield [__DIR__ . '/Source/latte/spaceless.latte', __DIR__ . '/Source/twig/spaceless.twig'];

        // complex
        yield [
            __DIR__ . '/ComplexSource/latte/arkadiuszkondas_default.latte',
            __DIR__ . '/ComplexSource/twig/arkadiuszkondas_default.twig',
        ];
    }

    public function provideDataForNMacros(): Iterator
    {
        yield [
            __DIR__ . '/Source/latte/n-macro/n-inner-foreach.latte',
            __DIR__ . '/Source/twig/n-macro/n-inner-foreach.twig',
        ];
        yield [__DIR__ . '/Source/latte/n-macro/n-if.latte', __DIR__ . '/Source/twig/n-macro/n-if.twig'];
        yield [__DIR__ . '/Source/latte/n-macro/n-ifset.latte', __DIR__ . '/Source/twig/n-macro/n-ifset.twig'];
        yield [__DIR__ . '/Source/latte/n-macro/n-foreach.latte', __DIR__ . '/Source/twig/n-macro/n-foreach.twig'];

        yield [__DIR__ . '/Source/latte/n-macro/n-class.latte', __DIR__ . '/Source/twig/n-macro/n-class.twig'];
    }

    public function provideDataForFilters(): Iterator
    {
        yield [__DIR__ . '/Source/latte/filter.latte', __DIR__ . '/Source/twig/filter.twig'];
        yield [
            __DIR__ . '/Source/latte/filter_with_arguments.latte',
            __DIR__ . '/Source/twig/filter_with_arguments.twig',
        ];
        yield [__DIR__ . '/Source/latte/filter_with_number.latte', __DIR__ . '/Source/twig/filter_with_number.twig'];
    }
}
