<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\VarPropertyComment;

use Symplify\CodingStandard\Sniffs\Commenting\VarPropertyCommentSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class VarPropertyCommentSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(VarPropertyCommentSniff::class, __DIR__);
    }
}
