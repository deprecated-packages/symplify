<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;

final class AttributeWithString
{
    #[Route(path: 'some_path', name: 'some')]
    public function run(): void
    {
    }
}
