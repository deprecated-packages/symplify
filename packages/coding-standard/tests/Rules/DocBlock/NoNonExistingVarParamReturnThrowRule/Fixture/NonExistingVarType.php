<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\DocBlock\NoNonExistingVarParamReturnThrowRule\Fixture;

final class NonExistingVarType
{
    /**
     * @var NonExistingClass
     */
    public $value;
}
