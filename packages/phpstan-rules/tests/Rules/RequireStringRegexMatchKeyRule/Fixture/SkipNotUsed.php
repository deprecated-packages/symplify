<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule\Fixture;

use Nette\Utils\Strings;

class SkipNotUsed
{
    private const REGEX = '#(a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
    }
}
