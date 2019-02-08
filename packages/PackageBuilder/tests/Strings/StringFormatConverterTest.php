<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Strings;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class StringFormatConverterTest extends TestCase
{
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    protected function setUp(): void
    {
        $this->stringFormatConverter = new StringFormatConverter();
    }

    /**
     * @dataProvider provideCasesForCamelCaseToUnderscore()
     */
    public function testCamelCaseToUnderscore(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->stringFormatConverter->camelCaseToUnderscore($input));
    }

    public function provideCasesForCamelCaseToUnderscore(): Iterator
    {
        yield ['hiTom', 'hi_tom'];
        yield ['GPWebPay', 'gp_web_pay'];
    }

    /**
     * @dataProvider provideCasesForUnderscoreToCamelCase()
     */
    public function testUnderscoreToCamelCase(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->stringFormatConverter->underscoreToCamelCase($input));
    }

    public function provideCasesForUnderscoreToCamelCase(): Iterator
    {
        yield ['hi_tom', 'hiTom'];
    }
}
