<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;

final class SkipCorrectName
{
    #[Route(path: 'api.json')]
    public function action()
    {
    }
}
