<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAttributteArgumentRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenAttributteArgumentRule\Source\AnotherAttribute;

#[AnotherAttribute(forbiddenKey: 'MissingClass')]
final class SomeClassWithForbiddenAttributeArgument
{

}
