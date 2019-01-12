<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Tests;

use Iterator;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;

final class LatteToTwigConverterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    protected function setUp(): void
    {
        $this->latteToTwigConverter = $this->container->get(LatteToTwigConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $latteFile, string $expectedTwigFile): void
    {
        $convertedFile = $this->latteToTwigConverter->convertFile($latteFile);
        $this->assertStringEqualsFile($expectedTwigFile, $convertedFile);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/variables.latte', __DIR__ . '/Source/expected-variables.twig'];
        yield [__DIR__ . '/Source/block-file.latte', __DIR__ . '/Source/expected-block-file.twig'];
        yield [__DIR__ . '/Source/filter.latte', __DIR__ . '/Source/expected-filter.twig'];
        yield [__DIR__ . '/Source/loops.latte', __DIR__ . '/Source/expected-loops.twig'];
        yield [__DIR__ . '/Source/conditions.latte', __DIR__ . '/Source/expected-conditions.twig'];
        yield [__DIR__ . '/Source/comment.latte', __DIR__ . '/Source/expected-comment.twig'];
        yield [__DIR__ . '/Source/capture.latte', __DIR__ . '/Source/expected-capture.twig'];
        yield [__DIR__ . '/Source/javascript.latte', __DIR__ . '/Source/expected-javascript.twig'];

        // new testing
        yield [__DIR__ . '/Source/latte/extends.latte', __DIR__ . '/Source/twig/extends.twig'];

        yield [__DIR__ . '/Source/latte/default.latte', __DIR__ . '/Source/twig/default.twig'];

        yield [__DIR__ . '/Source/latte/nested_variable.latte', __DIR__ . '/Source/twig/nested_variable.twig'];

        yield [
            __DIR__ . '/Source/latte/multiple_nested_variable.latte',
            __DIR__ . '/Source/twig/multiple_nested_variable.twig',
        ];

        yield [__DIR__ . '/Source/latte/first_last.latte', __DIR__ . '/Source/twig/first_last.twig'];
        yield [__DIR__ . '/Source/latte/filter.latte', __DIR__ . '/Source/twig/filter.twig'];
    }
}
