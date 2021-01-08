<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule\Fixture;

use Nette\Utils\Strings;

class NumericDimOtherNameMultipleValues
{
    private const REGEX = '#(a content)#';

    public function run()
    {
        $result = Strings::match('a content', self::REGEX);
        echo 'a statement before';
        if ($result[1] === '...') {

        } else {
            return $result[4];
        }
    }
}
