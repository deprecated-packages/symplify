<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule\Fixture;

final class SkipNormalArguments
{
    public function run()
    {
        return new \stdClass('value');
    }
}
