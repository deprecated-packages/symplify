<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Source;

interface ParentContractNullableInterface
{
    public function configure(?array $configuration);
}
