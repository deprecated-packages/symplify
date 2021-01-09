<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

trait SkipTraitUsingTrait
{
    use SkipSomeTrait;
}
