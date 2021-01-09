<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule\Fixture;

final class MissingNestedPrefixTest
{
    public function provideData(): \Iterator
    {
        yield [[__DIR__ . '/Fixture/CorrectNaming.php'], []];
    }
}
