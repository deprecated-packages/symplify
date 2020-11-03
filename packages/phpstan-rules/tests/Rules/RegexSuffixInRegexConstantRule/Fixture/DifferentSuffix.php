<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RegexSuffixInRegexConstantRule\Fixture;

use Nette\Utils\Strings;

final class DifferentSuffix
{
    public const SOME_NAME = '#some\s+name#';

    public function run($value)
    {
        $somePath = Strings::match($value, self::SOME_NAME);
    }
}
