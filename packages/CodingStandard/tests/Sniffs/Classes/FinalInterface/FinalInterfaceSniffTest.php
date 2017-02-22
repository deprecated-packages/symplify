<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes\FinalInterface;

use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class FinalInterfaceSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(FinalInterfaceSniff::class, __DIR__);
    }
}
