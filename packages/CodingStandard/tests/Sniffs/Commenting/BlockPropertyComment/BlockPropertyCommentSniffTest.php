<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\BlockPropertyComment;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\Commenting\BlockPropertyCommentSniff;

/**
 * @covers \SymplifyCodingStandard\Sniffs\Commenting\BlockPropertyCommentSniff
 */
final class BlockPropertyCommentSniffTest extends TestCase
{

    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(BlockPropertyCommentSniff::NAME);

        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
    }


    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner('SymplifyCodingStandard.Commenting.BlockPropertyComment');

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);
    }
}
