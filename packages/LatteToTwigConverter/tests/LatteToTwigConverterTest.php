<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Tests;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;

final class LatteToTwigConverterTest extends TestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    protected function setUp(): void
    {
        $this->latteToTwigConverter = new LatteToTwigConverter();
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
        yield [
            __DIR__ . '/LatteToTwigConverterSource/file.latte',
            __DIR__ . '/LatteToTwigConverterSource/expected-file.twig',
        ];

        yield [
            __DIR__ . '/LatteToTwigConverterSource/block-file.latte',
            __DIR__ . '/LatteToTwigConverterSource/expected-block-file.twig',
        ];
    }
}
