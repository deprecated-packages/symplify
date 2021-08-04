<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule\Source\SomeToString;

final class SkipToString
{
    public function run()
    {
        $someToString = new SomeToString('whatever');
        return sprintf('String key %s', $someToString);
    }
}
