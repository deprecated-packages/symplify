<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\InterfaceName;

use Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class InterfaceNameSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(InterfaceNameSniff::class, __DIR__);
    }
}
