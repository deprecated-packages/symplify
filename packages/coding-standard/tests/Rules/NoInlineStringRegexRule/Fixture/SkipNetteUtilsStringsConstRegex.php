<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoInlineStringRegexRule\Fixture;

use Nette\Utils\Strings;

final class SkipNetteUtilsStringsConstRegex
{
    const SOME_PATTERN = '#some_pattern#';

    public function run()
    {
        return Strings::match('subject', self::SOME_PATTERN);
    }
}
