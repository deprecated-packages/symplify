<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNodeRule\Fixture;

final class EmptyCall
{
    public function run()
    {
        return empty($value);
    }
}
