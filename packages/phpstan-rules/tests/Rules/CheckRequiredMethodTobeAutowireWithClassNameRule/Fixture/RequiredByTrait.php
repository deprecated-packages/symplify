<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\Fixture;

trait RequiredByTrait
{
    /**
     * @required
     */
    public function autowireWrong()
    {
    }
}
