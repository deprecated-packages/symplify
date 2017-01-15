<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes\FinalInterface;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;

final class FinalInterfaceSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(FinalInterfaceSniff::NAME, __DIR__);
    }
}
