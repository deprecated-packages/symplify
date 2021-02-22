<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

final class SkipSingleSide
{
    public function run()
    {
        return sprintf('hey %s</>', 'one');
    }
}
