<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\Fixture;

final class SkipDocBlock
{
    public function run(string $type)
    {
        $doc = sprintf('/** @var %s */', $type);
    }
}
