<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule\Fixture;

final class SkipValidTernary
{
    public function run(bool $hasDonut)
    {
        return sprintf('I sure do like my %s', $hasDonut ? 'donut' : 'coffee');
    }
}
