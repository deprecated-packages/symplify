<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoTraitExceptRequiredAutowireRule\Fixture;

trait SomeTraitWithPrivateMethodRequired
{
    /**
     * @required
     */
    private function run()
    {
    }
}
