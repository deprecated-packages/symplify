<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Set\Indent;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @covers https://github.com/Symplify/Symplify/issues/777
 */
final class IndentTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider wrongToFixedCases()
     */
    public function test(string $wrongFile, string $correctFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $correctFile);
    }

    public function wrongToFixedCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
