<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipNotSprintf
{
    public function run()
    {
        echo strlen('test');
    }
}
