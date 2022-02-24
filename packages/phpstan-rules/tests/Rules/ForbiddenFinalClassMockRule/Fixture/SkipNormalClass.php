<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Source\FinalClass;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Source\NormalClass;

final class SkipNormalClass extends TestCase
{
    public function test()
    {
        $this->getMockBuilder(NormalClass::class);
    }
}
