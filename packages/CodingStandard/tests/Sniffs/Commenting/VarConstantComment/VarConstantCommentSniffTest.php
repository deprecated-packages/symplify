<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\VarConstantComment;

use Iterator;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff;
use Symplify\EasyCodingStandard\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff
 */
final class VarConstantCommentSniffTest extends AbstractCheckerTestCase
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
    }

    protected function createSniff(): Sniff
    {
        return new VarConstantCommentSniff();
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
