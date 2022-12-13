<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicStaticPropertyRule\Fixture;

final class SkipPrivateProperty
{
    private function run()
    {
    }

    private $somePublicStaticProperty;
}
