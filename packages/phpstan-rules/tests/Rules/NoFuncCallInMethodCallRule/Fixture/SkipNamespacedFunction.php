<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use function \Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture\Functions\namespaced;

final class SkipNamespacedFunction
{
    public function something(): void
    {
        $this->process(namespaced());
    }

    private function process(string $ref)
    {
    }
}
