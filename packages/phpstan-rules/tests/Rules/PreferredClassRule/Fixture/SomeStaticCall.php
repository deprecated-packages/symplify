<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Fixture;

use DateTime;

final class SomeStaticCall
{
    public function run()
    {
        $lastErrors = DateTime::getLastErrors();
    }
}
