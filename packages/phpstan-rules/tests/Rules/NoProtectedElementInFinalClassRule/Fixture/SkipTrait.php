<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

trait SkipTrait
{
    abstract protected function run();
    protected $x;
}
