<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Source\FinalClass;

final class CreateMockMethod extends TestCase
{
    public function test()
    {
        $this->createMock(FinalClass::class);
    }
}
