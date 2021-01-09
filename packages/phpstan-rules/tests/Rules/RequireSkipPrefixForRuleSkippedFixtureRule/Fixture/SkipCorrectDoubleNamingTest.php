<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule\Fixture;

final class SkipCorrectDoubleNamingTest
{
    public function provideData(): \Iterator
    {
        yield [[
            __DIR__ . '/Fixture/SkipCorrectNaming.php',
            __DIR__ . '/Fixture/SkipCorrectAgain.php',
        ], []];
    }
}
