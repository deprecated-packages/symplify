<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use function Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Source\some_function;

final class SkipNamespacedFunction
{
    public function something(): void
    {
        $this->process(some_function());
    }

    private function process(string $ref)
    {
    }
}
