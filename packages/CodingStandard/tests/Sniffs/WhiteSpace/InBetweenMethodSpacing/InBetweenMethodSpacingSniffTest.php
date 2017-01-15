<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\InBetweenMethodSpacing;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\WhiteSpace\InBetweenMethodSpacingSniff;

final class InBetweenMethodSpacingSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(InBetweenMethodSpacingSniff::NAME, __DIR__);
    }
}
