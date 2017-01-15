<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\PropertiesMethodsMutualSpacing;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\WhiteSpace\PropertiesMethodsMutualSpacingSniff;

final class PropertiesMethodsMutualSpacingSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(PropertiesMethodsMutualSpacingSniff::NAME, __DIR__);
    }
}
