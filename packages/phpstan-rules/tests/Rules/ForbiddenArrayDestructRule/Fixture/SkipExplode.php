<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\Fixture;

final class SkipExplode
{
    public function run()
    {
        [$one, $two] = explode('::', 'SomeClass::SOME_CONSTANTS');
    }
}
