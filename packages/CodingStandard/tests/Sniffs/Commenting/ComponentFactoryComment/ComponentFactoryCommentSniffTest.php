<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\ComponentFactoryComment;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use SymplifyCodingStandard\Sniffs\Commenting\ComponentFactoryCommentSniff;

final class ComponentFactoryCommentSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(ComponentFactoryCommentSniff::NAME, __DIR__);
    }
}
