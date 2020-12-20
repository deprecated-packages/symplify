<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipNotRegexMatchResult
{
    public function run()
    {
        $matches = [
            1 => 'content',
        ];

        echo $matches[1];
    }
}
