<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule\Source\DifferentTrait;

final class SkipDifferentTrait
{
    use DifferentTrait;
}
