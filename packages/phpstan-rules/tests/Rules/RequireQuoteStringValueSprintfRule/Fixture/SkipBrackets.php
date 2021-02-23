<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

final class SkipBrackets
{
    public function run()
    {
        return sprintf('[%s] (%s)', 'one', 'two');
    }
}
