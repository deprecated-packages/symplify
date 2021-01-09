<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule\Fixture;

use PHPStan\Rules\DeadCode\UnusedPrivateConstantRule;
use PHPStan\Testing\RuleTestCase;

final class MissingPrefixTest
{
    public function provideData(): \Iterator
    {
        yield [__DIR__ . '/Fixture/CorrectNaming.php', []];
    }
}
