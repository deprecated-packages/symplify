<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule\Fixture;

use Nette\Utils\Strings;

class NumericDimInsideIfCond
{
    private const REGEX = '#(a content)#';

    public function run()
    {
        if ($matches = Strings::match('a content', self::REGEX)) {
            echo 'a statement before';
            echo $matches[1];
        }
    }
}
