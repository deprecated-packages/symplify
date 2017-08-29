<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Property\DynamicProperty;

use Symplify\CodingStandard\Sniffs\Property\DynamicPropertySniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class DynamicPropertyTest extends AbstractSniffTestCase
{
    public function testDetection(): void
    {
        $this->runSniffTestForDirectory(DynamicPropertySniff::class, __DIR__);
    }
}
