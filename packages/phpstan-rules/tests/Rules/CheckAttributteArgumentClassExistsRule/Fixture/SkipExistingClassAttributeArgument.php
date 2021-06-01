<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\Source\ExistingClass;
use Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\Source\SomeAttribute;

#[SomeAttribute(className: ExistingClass::class)]
final class SkipExistingClassAttributeArgument
{
}
