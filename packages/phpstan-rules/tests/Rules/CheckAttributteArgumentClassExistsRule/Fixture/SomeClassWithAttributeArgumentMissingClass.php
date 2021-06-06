<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\Source\SomeAttribute;

#[SomeAttribute(className: 'MissingClass')]
final class SomeClassWithAttributeArgumentMissingClass
{

}
