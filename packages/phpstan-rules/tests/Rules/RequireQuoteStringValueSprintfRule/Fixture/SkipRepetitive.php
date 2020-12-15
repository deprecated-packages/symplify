<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipRepetitive
{
    public function run()
    {
        echo sprintf('%s%s test', 'value 1', 'value 2');
    }
}
