<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule\Source\SomeToString;

final class SkipToString
{
    public function run()
    {
        $someToString = new SomeToString('whatever');
        return sprintf('String key %s', $someToString);
    }
}
