<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule\Fixture;

final class ClassWithNamedArguments
{
    public function run()
    {
        return strlen(string: 'value');
    }
}
