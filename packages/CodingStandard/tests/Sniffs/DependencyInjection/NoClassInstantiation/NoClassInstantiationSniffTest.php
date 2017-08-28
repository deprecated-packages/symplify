<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DependencyInjection\NoClassInstantiation;

use Symplify\CodingStandard\Sniffs\DependencyInjection\NoClassInstantiationSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class NoClassInstantiationSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(NoClassInstantiationSniff::class, __DIR__);
    }
}
