<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\InBetweenMethodSpacing;

use Symplify\CodingStandard\Sniffs\WhiteSpace\InBetweenMethodSpacingSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class InBetweenMethodSpacingSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(InBetweenMethodSpacingSniff::class, __DIR__);
    }
}
