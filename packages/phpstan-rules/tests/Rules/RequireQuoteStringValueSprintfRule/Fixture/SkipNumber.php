<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

final class SkipNumber
{
    public function run()
    {
        return sprintf('Return %d values', 'one');
    }
}
