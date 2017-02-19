<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\PropertiesMethodsMutualSpacing;

use Symplify\CodingStandard\Sniffs\WhiteSpace\PropertiesMethodsMutualSpacingSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class PropertiesMethodsMutualSpacingSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(PropertiesMethodsMutualSpacingSniff::class, __DIR__);
    }
}
