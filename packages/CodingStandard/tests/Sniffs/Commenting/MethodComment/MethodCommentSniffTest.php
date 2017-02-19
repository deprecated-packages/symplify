<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\MethodComment;

use Symplify\CodingStandard\Sniffs\Commenting\MethodCommentSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class MethodCommentSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(MethodCommentSniff::class, __DIR__);
    }
}
