<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\Source\KnownType;

final class SkipDynamicName
{
    public function run($unknownType)
    {
        $unknownType->{$name};
    }

    public function runOnType(KnownType $knownType)
    {
        $knownType->{$name};
    }
}
