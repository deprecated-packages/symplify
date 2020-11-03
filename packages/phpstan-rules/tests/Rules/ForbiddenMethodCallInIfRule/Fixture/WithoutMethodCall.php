<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallInIfRule\Fixture;

class WithoutMethodCall
{
    public function execute($arg)
    {
        $data = [];
        if ($data === []) {

        } elseif ($data !== []) {

        }
    }

}
