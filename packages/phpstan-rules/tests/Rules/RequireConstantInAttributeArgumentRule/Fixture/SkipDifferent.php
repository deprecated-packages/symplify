<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\Fixture;

final class SkipDifferent
{
    #[Route(name: 'some_path')]
    public function run(): void
    {
    }
}
