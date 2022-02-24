<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Source\FinalClass;

final class MockOfFinalClass extends TestCase
{
    public function test()
    {
        $this->getMockBuilder(FinalClass::class);
    }
}
