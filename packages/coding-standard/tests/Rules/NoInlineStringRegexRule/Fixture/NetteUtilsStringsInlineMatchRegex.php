<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoInlineStringRegexRule\Fixture;

use Nette\Utils\Strings;

final class NetteUtilsStringsInlineMatchRegex
{
    public function run()
    {
        return Strings::match('subject', '#some_pattern#');
    }
}
