<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\CommentedOutCode;

use Iterator;
use Symplify\EasyCodingStandard\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff
 */
final class CommentedOutCodeSniffTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/wrong/wrong4.php.inc'];
    }

    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrect(string $file): void
    {
        $this->doTestCorrectFile($file);
    }

    public function provideCorrectCases(): Iterator
    {
        yield [__DIR__ . '/correct/correct.php.inc'];
        yield [__DIR__ . '/correct/correct2.php.inc'];
        yield [__DIR__ . '/correct/correct3.php.inc'];
        yield [__DIR__ . '/correct/correct4.php.inc'];
        yield [__DIR__ . '/correct/correct5.php.inc'];
        yield [__DIR__ . '/correct/correct6.php.inc'];
        yield [__DIR__ . '/correct/correct7.php.inc'];
        yield [__DIR__ . '/correct/correct8.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
