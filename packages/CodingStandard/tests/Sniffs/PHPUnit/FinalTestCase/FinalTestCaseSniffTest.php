<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\PHPUnit\FinalTestCase;

use Symplify\CodingStandard\Sniffs\PHPUnit\FinalTestCaseSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class FinalTestCaseSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(FinalTestCaseSniff::class, __DIR__);
    }
}
