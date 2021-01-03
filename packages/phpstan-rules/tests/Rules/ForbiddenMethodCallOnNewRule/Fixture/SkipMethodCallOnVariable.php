<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnNewRule\Fixture;

use DateTime;

class SkipMethodCallOnVariable
{
    public function run()
    {
        $d = new DateTime('2020-01-01');
        $d->format('Y-m-d');
    }
}
