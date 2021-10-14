<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipScopedClosures
{
    public function run($callable, $secondCallable)
    {
        $callable->on(function () {
            $error = true;
            $error(100);
        });

        $secondCallable->on(function () {
            $error = false;
            $error(100);
        });

        return [$callable, $secondCallable];
    }
}
