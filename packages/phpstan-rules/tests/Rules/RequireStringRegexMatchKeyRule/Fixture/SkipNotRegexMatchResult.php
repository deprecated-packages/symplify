<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule\Fixture;

class SkipNotRegexMatchResult
{
    public function run()
    {
        $matches = [
            1 => 'content',
        ];

        echo 'a statement before';
        echo $matches[1];
    }
}
