<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\Fixture;

final class SkipAutowireName
{
    /**
     * @required
     */
    public function autowire()
    {
    }
}
