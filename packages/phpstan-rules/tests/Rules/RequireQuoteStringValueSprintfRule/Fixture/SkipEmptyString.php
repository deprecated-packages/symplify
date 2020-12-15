<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipEmptyString
{
    public function run()
    {
        echo sprintf(' ');
    }
}
