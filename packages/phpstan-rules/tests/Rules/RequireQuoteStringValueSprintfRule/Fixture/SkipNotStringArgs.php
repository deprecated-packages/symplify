<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class SkipNotStringArgs
{
    public function run($value)
    {
        echo sprintf($value, 'test');
    }
}
