<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

final class SkipScopedClosures
{
    public function run()
    {
        $callable = function () {
            $error = true;
        };

        $secondCallable = function () {
            $error = false;
        };
    }
}
