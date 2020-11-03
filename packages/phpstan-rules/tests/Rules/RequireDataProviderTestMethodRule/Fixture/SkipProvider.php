<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireDataProviderTestMethodRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireDataProviderTestMethodRule\Source\AbstractSomeTestClass;

final class SkipProvider extends AbstractSomeTestClass
{
    public function testThis($value)
    {
    }
}
