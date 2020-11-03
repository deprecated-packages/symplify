<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\Fixture;

use Nette\Utils\Strings;

final class SkipSingleLetter
{
    public function run()
    {
        return Strings::match('some value', '#(\d+)#');
    }
}
