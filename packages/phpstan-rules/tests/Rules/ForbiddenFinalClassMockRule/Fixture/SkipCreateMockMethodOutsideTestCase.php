<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Source\FinalClass;

final class SkipCreateMockMethodOutsideTestCase
{
    public function test()
    {
        $this->createMock(FinalClass::class);
    }

    public function createMock(string $className)
    {
    }
}
