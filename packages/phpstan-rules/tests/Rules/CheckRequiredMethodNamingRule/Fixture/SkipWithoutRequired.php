<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\Fixture;

final class SkipWithoutRequired
{
    /**
     * @param int $foo
     */
    public function run(int $foo)
    {
    }
}
