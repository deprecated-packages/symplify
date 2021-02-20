<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Source\ParentContractNullableInterface;

abstract class SkipParentContract implements ParentContractNullableInterface
{
    public function configure(?array $configuration = null)
    {
    }
}
