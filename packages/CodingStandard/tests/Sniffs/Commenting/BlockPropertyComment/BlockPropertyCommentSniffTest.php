<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\BlockPropertyComment;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Commenting\BlockPropertyCommentSniff;

final class BlockPropertyCommentSniffTest extends AbstractSniffTestCase
{
    public function testDetection()
    {
        $this->runSniffTestForDirectory(BlockPropertyCommentSniff::NAME, __DIR__);
    }
}
