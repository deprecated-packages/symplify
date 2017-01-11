<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\VarPropertyComment;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use SymplifyCodingStandard\Sniffs\Commenting\VarPropertyCommentSniff;

final class VarPropertyCommentSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(VarPropertyCommentSniff::NAME, __DIR__);
    }
}
