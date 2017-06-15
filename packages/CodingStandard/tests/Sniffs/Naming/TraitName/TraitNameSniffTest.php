<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\TraitName;

use Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class TraitNameSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(TraitNameSniff::class, __DIR__);
    }
}
