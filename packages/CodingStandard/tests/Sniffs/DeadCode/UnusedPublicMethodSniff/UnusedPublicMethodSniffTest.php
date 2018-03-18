<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DeadCode\UnusedPublicMethodSniff;

use Iterator;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class UnusedPublicMethodSniffTest extends AbstractSniffTestCase
{
    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

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
    }

    protected function createSniff(): Sniff
    {
        return new UnusedPublicMethodSniff();
    }
}
