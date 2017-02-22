<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\AbstractClassName;

use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class AbstractClassNameSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(AbstractClassNameSniff::class, __DIR__);
    }
}
