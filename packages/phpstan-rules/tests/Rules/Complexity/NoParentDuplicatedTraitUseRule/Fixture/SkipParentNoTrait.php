<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Source\SomeParentClassWithoutTraitUse;
use Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Source\SomeTrait;

final class SkipParentNoTrait extends SomeParentClassWithoutTraitUse
{
    use SomeTrait;
}
