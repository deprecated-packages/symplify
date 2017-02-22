<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructures\YodaCondition;

use Symplify\CodingStandard\Sniffs\ControlStructures\YodaConditionSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class YodaConditionSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(YodaConditionSniff::class, __DIR__);
    }
}
