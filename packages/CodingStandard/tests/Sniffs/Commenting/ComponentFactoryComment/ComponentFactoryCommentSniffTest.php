<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\ComponentFactoryComment;

use Symplify\CodingStandard\Sniffs\Commenting\ComponentFactoryCommentSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ComponentFactoryCommentSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(ComponentFactoryCommentSniff::class, __DIR__);
    }
}
