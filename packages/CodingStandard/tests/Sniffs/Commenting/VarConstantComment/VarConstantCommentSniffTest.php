<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\VarPropertyComment;

use Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class VarConstantCommentSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(VarConstantCommentSniff::class, __DIR__);
    }
}
