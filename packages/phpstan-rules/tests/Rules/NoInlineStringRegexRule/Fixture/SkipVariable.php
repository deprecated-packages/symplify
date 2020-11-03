<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\Fixture;

use Nette\Utils\Strings;

final class SkipVariable
{
    const EXPLICIT_NAME = '#some_REGEX#';

    public function run()
    {
        $someVariable = self::EXPLICIT_NAME;
        $isFuncCall = preg_match($someVariable, 'subject ');

        $isStaticMethod = Strings::match( 'subject', $someVariable);
    }
}
