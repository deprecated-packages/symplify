<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\MethodComment;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Commenting\MethodCommentSniff;

final class MethodCommentSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(MethodCommentSniff::NAME, __DIR__);
    }
}
