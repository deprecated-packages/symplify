<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

trait SomeAutowiredTrait
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
