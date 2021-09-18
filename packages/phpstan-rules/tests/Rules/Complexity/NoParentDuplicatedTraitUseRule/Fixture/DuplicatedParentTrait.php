<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Source\SomeParentClassWithTraitUse;
use Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Source\SomeTrait;

final class DuplicatedParentTrait extends SomeParentClassWithTraitUse
{
    use SomeTrait;
}
