<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

final class SkipClassWithTrait
{
    use SkipTraitUsingTrait;

    public function anotherCall()
    {
        return $this->anotherMethod();
    }
}
