<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class NoQuoteInFirstOrLast
{
    public function run()
    {
        echo sprintf('%s value', 'value');
        echo sprintf('value %s', 'value');
    }
}
