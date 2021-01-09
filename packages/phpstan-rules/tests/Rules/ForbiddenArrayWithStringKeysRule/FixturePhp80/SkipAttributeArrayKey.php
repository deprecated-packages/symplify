<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\FixturePhp80;

use Symfony\Component\Routing\Annotation\Route;

final class SkipAttributeArrayKey
{
    #[Route('/blog/{slug}', name: 'post_detail', requirements: [
        'slug' => '\d+\/\d+.+',
    ])]
    public function run(): string
    {
        return 'some_content';
    }
}
