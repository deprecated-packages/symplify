<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\NetteRefactoring\InjectToConstructorInjection;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use SymplifyCodingStandard\Sniffs\NetteRefactoring\InjectToConstructorInjectionSniff;

/**
 * Constructor injection should be used over @inject annotation and inject* methods.
 * Except abstract BasePresenter.
 */
final class InjectToConstructorInjectionSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(InjectToConstructorInjectionSniff::class, __DIR__);
    }
}
