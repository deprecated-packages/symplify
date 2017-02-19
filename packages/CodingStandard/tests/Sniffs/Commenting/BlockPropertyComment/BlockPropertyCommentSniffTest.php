<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\BlockPropertyComment;

use Symplify\CodingStandard\Sniffs\Commenting\BlockPropertyCommentSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class BlockPropertyCommentSniffTest extends AbstractSniffTestCase
{
    public function testDetection()
    {
        $this->runSniffTestForDirectory(BlockPropertyCommentSniff::class, __DIR__);
    }
}
