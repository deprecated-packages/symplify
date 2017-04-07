<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Refactorer\NetteDI\InjectToConstructorInjection;

use Symplify\CodingStandard\Refactorer\NetteDI\InjectToConstructorInjectionSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

/**
 * Constructor injection should be used over @inject annotation and inject* methods.
 * Except abstract BasePresenter.
 */
final class InjectToConstructorInjectionSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(InjectToConstructorInjectionSniff::class, __DIR__);
    }
}
