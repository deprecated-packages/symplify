<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

final class SkipReturnedNoVoid
{
    public function run()
    {
        return $this->getResult();
    }

    private function getResult()
    {
        return [];
    }
}
