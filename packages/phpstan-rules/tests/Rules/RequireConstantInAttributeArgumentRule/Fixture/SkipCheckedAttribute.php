<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;

final class SkipCheckedAttribute
{
    #[Route(path: 'some_path')]
    public function run(): void
    {
    }
}
