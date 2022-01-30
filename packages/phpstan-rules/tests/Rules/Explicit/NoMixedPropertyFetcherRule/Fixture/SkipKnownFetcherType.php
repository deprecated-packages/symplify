<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\Source\KnownType;

final class SkipKnownFetcherType
{
    public function run(KnownType $knownType)
    {
        $knownType->name;
    }
}
