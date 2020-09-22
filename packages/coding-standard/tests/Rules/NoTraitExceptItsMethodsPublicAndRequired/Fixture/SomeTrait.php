<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsPublicAndRequired\Fixture;

trait SomeTrait
{
    private function run()
    {
    }
}
