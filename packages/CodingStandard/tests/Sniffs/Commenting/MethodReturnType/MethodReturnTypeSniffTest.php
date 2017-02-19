<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\MethodReturnType;

use Symplify\CodingStandard\Sniffs\Commenting\MethodCommentReturnTypeSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class MethodReturnTypeSniffTest extends AbstractSniffTestCase
{
    public function testDetection()
    {
        $this->runSniffTestForDirectory(MethodCommentReturnTypeSniff::class, __DIR__);
    }
}
