<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Source\KnownType;

final class SkipStaticVar
{
    public function run()
    {
        static $static = null;

        if (!$static) {
            $static = new KnownType();
        }

        $static->call();
    }
}
