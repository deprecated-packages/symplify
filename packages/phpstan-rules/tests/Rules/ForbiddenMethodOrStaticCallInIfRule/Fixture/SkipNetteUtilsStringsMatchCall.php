<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\Fixture;

use Nette\Utils\Strings;

final class SkipNetteUtilsStringsMatchCall
{
    public function run()
    {
        if (Strings::match('content', '#c#')) {
            return true;
        }

        return false;
    }
}
