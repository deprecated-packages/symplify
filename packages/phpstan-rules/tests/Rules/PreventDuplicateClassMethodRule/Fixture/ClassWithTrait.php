<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class ClassWithTrait
{
    use TraitUsingTrait;

    public function anotherCall()
    {
        return $this->anotherMethod();
    }
}
