<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenCallOnTypeRule\Fixture;

use Nette\Utils\Strings;

final class CallOnNetteUtilsStrings
{
    public function call()
    {
        Strings::endsWith($a, 'a');
    }
}
