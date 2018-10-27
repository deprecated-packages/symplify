<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\PreferredClassSniff;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff
 */
final class PreferredClassSniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongCases()
     */
    public function testWrong(string $file): void
    {
        $this->doTestWrongFile($file);
    }

    public function provideWrongCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc'];
        yield [__DIR__ . '/wrong/wrong2.php.inc'];
        yield [__DIR__ . '/wrong/wrong3.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
