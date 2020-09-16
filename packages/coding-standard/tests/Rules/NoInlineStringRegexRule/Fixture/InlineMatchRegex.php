<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoInlineStringRegexRule\Fixture;

final class InlineMatchRegex
{
    public function run()
    {
        return preg_match('#some_REGEX#', 'subject ');
    }
}
