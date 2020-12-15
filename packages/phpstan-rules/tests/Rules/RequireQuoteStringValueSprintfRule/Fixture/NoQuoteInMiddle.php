<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

class NoQuoteInMiddle
{
    public function run()
    {
        echo sprintf('a sentence %s value', 'value');
    }
}
