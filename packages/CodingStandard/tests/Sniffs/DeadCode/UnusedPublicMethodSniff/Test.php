<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DeadCode\UnusedPublicMethodSniff;

use Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class Test extends AbstractSniffTestCase
{
    public function testFix(): void
    {
        $this->runSniffTestForDirectory(UnusedPublicMethodSniff::class, __DIR__);
    }
}
