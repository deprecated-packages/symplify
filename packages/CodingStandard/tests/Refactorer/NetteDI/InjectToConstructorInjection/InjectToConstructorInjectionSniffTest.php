<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Refactorer\NetteDI\InjectToConstructorInjection;

use Symplify\CodingStandard\Refactorer\NetteDI\InjectToConstructorInjectionSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class InjectToConstructorInjectionSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(InjectToConstructorInjectionSniff::class, __DIR__);
    }
}
