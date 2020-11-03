<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireDataProviderTestMethodRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\RequireDataProviderTestMethodRule\Source\AbstractSomeTestClass;

final class SkipProvider extends AbstractSomeTestClass
{
    public function testThis($value)
    {
    }
}
