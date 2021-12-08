<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule\Source\SimpleAttribute;

final class SkipAttributeNamedArguments
{
    #[SimpleAttribute(value: 'some_value')]
    public function run()
    {
    }
}
