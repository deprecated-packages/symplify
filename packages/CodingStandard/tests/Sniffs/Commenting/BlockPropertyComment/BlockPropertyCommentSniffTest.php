<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\BlockPropertyComment;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\Commenting\BlockPropertyCommentSniff;

final class BlockPropertyCommentSniffTest extends TestCase
{
    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(BlockPropertyCommentSniff::NAME);

        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php.inc'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
    }

    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(BlockPropertyCommentSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
    }
}
