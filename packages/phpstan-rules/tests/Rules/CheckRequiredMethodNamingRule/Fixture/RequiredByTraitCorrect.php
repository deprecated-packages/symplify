<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\Fixture;

trait RequiredByTraitCorrect
{
    /**
     * @required
     */
    public function autowireWrongRequiredByTraitCorrect()
    {
    }
}
