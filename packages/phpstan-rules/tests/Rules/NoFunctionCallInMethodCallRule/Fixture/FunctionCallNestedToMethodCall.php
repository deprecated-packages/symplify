<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFunctionCallInMethodCallRule\Fixture;

final class FunctionCallNestedToMethodCall
{
    public function run($value): void
    {
        $this->nothing(strlen('fooo'));
    }

    private function nothing($value)
    {
        return $value;
    }
}
