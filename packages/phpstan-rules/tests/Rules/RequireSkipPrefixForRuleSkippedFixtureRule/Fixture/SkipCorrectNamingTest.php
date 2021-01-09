<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule\Fixture;

final class SkipCorrectNamingTest
{
    public function provideData(): \Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCorrectNaming.php', []];
    }
}
