<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicStaticPropertyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicStaticPropertyRule\Source\SkipExternallyUsedPublicStaticProperty;

final class AnotherClassUsingPublicStaticProperty
{
    public function run()
    {
        return SkipExternallyUsedPublicStaticProperty::$name;
    }
}
