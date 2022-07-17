<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

use Symfony\Component\Finder\Finder;

final class SkipMaskFinder
{
    public function run($sources)
    {
        $finder = new Finder();
        $finder->files()
            ->in($sources)
            ->name('*.php.inc')
            ->path('Fixture')
            ->sortByName();
    }
}
