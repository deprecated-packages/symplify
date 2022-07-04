<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

final class SkipPublicApiClassMethod
{
    /**
     * @api
     */
    public function freeForAll()
    {
    }
}
