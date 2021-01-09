<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

trait SkipSomeAutowiredTrait
{
    protected $someDependency;

    /**
     * @required
     */
    public function autowiredSomeAutowiredTrait($someDependency)
    {
        $this->someDependency = $someDependency;
    }
}
