<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\Fixture;

trait RequiredByTraitCorrect
{
    /**
     * @required
     */
    public function autowireWrongRequiredByTraitCorrect()
    {
    }
}
