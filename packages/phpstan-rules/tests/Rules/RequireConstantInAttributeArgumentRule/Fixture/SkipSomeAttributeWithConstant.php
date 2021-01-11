<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;
use Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\Source\RouteName;

final class SkipSomeAttributeWithConstant
{
    #[Route(path: 'some_path', name: RouteName::SOME_CONST)]
    public function run(): void
    {
    }
}
