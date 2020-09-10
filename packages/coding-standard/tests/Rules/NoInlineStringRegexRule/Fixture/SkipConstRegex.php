<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoInlineStringRegexRule\Fixture;

final class SkipConstRegex
{
    const EXPLICIT_NAME = '#some_pattern#';

    public function run()
    {
        return preg_match(self::EXPLICIT_NAME, 'subject ');
    }
}
