<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

final class LocallyUsedPublicMethod
{
    private function run()
    {
        $this->runHere();
    }

    public function runHere()
    {
    }
}
