<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ClassNameSuffixByParentSniff;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff
 */
final class ClassNameSuffixByParentSniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongCases()
     */
    public function testWrongFiles(string $wrongFile): void
    {
        $this->doTestWrongFile($wrongFile);
    }

    public function provideWrongCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc'];
        yield [__DIR__ . '/wrong/wrong2.php.inc'];
        yield [__DIR__ . '/wrong/wrong3.php.inc'];
        yield [__DIR__ . '/wrong/wrong4.php.inc'];
    }

    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrectFiles(string $wrongFile): void
    {
        $this->doTestWrongFile($wrongFile);
    }

    public function provideCorrectCases(): Iterator
    {
        yield [__DIR__ . '/correct/correct.php.inc'];
        yield [__DIR__ . '/correct/correct2.php.inc'];
        yield [__DIR__ . '/correct/correct3.php.inc'];
        yield [__DIR__ . '/correct/correct4.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
