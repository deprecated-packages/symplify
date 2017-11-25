<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\VarConstantComment;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class VarConstantCommentSniffTest extends AbstractSniffTestCase
{
    /**
     * @dataProvider provideCases()
     */
    public function testFix(string $input, string $expected): void
    {
        $this->doTest($input, $expected);
//        $this->runSniffTestForDirectory(VarConstantCommentSniff::class, __DIR__);
    }

    /**
     * @return string[][]
     */
    public function provideCases(): array
    {
        return [
            // wrong => fixed

        ];
    }

    protected function createSniff(): Sniff
    {
        return new VarConstantCommentSniff();
    }
}
