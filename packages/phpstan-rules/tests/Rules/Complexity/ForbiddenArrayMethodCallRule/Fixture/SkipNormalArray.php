<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenArrayMethodCallRule\Fixture;

final class SkipNormalArray
{
    public function run()
    {
        return ['one', 'two'];
    }
}
