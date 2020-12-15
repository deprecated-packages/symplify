<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipSprintfArgsOne
{
    public function run()
    {
        echo sprintf('test');
    }
}
