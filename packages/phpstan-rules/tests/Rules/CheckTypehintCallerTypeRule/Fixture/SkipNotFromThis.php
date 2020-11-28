<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

final class SkipNotFromThis
{
    public function run()
    {
        $obj = new \DateTime('now');
        $obj->format('Y-m-d');
    }
}
