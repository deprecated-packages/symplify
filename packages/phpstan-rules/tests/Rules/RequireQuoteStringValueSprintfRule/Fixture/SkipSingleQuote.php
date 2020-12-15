<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipSingleQuote
{
    public function run(string $regexMessage)
    {
        echo sprintf(" - '%s'", $regexMessage);
    }
}
