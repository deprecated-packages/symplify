<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\Fixture;

use Nette\Utils\Strings;

final class SkipNetteUtilsStringsConstRegex
{
    const SOME_REGEX = '#some_REGEX#';

    public function run()
    {
        return Strings::match('subject', self::SOME_REGEX);
    }
}
