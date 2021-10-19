<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipScopedClosures
{
    private $someExternal;

    public function run($callable, $secondCallable)
    {
        $callable->on(function () {
            $error = $this->someExternal;
            $error(100);
        });

        $secondCallable->on(function () {
            $error = $this->someExternal;
            $error(100);
        });

        return [$callable, $secondCallable];
    }
}
