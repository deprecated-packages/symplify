<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer
 */
final class ConfiguredLineLengthFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider wrongToFixedCases()
     */
    public function test(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function wrongToFixedCases(): Iterator
    {
        yield [__DIR__ . '/wrong/configured-wrong.php.inc', __DIR__ . '/fixed/configured-fixed.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/configured-config.yml';
    }
}
