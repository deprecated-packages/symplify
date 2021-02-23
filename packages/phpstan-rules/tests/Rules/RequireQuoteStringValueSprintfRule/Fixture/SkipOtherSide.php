<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

final class SkipOtherSide
{
    public function run()
    {
        return sprintf('--%s hey', 'one');
    }
}
