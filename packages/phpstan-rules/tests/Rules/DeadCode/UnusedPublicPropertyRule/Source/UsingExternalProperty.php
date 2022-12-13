<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicPropertyRule\Source;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicPropertyRule\Fixture\LocalyUsedPublicProperty;

final class UsingExternalProperty
{
    public function run(LocalyUsedPublicProperty $localyUsedPublicProperty)
    {
        return $localyUsedPublicProperty->name;
    }
}
