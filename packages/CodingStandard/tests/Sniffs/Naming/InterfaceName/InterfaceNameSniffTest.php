<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\InterfaceName;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff;

final class InterfaceNameSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(InterfaceNameSniff::NAME, __DIR__);
    }
}
