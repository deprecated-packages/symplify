<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule\Fixture;

class SkipCallLocalStaticMethod
{
    public function run()
    {
        self::callstatic();
    }

    private static function callstatic()
    {
    }
}
