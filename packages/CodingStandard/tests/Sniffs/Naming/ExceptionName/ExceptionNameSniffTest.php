<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ExceptionName;

use Symplify\CodingStandard\Sniffs\Naming\ExceptionNameSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ExceptionNameSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(ExceptionNameSniff::class, __DIR__);
    }
}
