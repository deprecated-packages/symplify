<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class SkipMethodExists
{
    public function run()
    {
        if (method_exists($this, 'run')) {
            return true;
        }

        return false;
    }
}
