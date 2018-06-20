<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ClassNameSuffixByParentSniff;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff
 */
final class ListConfiguredTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function provideWrongToFixedCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong5.php.inc', __DIR__ . '/fixed/fixed5.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/list-configured-config.yml';
    }
}
