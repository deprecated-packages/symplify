<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\Fixture;

trait RequiredByTraitCorrect
{
    /**
     * @required
     */
    public function autowireWrongRequiredByTraitCorrect()
    {
    }
}
