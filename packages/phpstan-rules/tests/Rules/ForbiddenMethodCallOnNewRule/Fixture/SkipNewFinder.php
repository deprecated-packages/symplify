<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnNewRule\Fixture;

use Symfony\Component\Finder\Finder;

final class SkipNewFinder
{
    public function run()
    {
        return (new Finder())->files();
    }
}
