<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructures\NewClass;

use Symplify\CodingStandard\Sniffs\ControlStructures\NewClassSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class NewClassSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(NewClassSniff::class, __DIR__);
    }
}
