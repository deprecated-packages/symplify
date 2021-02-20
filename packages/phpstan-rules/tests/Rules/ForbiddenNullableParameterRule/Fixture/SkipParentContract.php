<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;

abstract class SkipParentContract implements ConfigurableFixerInterface
{
    public function configure(?array $configuration = null)
    {
    }
}
