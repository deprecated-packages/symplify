<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

final class ReturnedNoVoid
{
    public function run()
    {
        $this->getResult();
    }

    private function getResult(): array
    {
        return [];
    }
}
