<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\MethodReturnType;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Commenting\MethodCommentReturnTypeSniff;

final class MethodReturnTypeSniffTest extends AbstractSniffTestCase
{
    public function testDetection()
    {
        $this->runSniffTestForDirectory(MethodCommentReturnTypeSniff::NAME, __DIR__);
    }
}
