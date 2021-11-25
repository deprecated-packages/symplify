<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule\Fixture;

final class SomeVoidAssign
{
    public function run()
    {
        $value = $this->getNothing();
    }

    public function getNothing(): void
    {
    }
}
