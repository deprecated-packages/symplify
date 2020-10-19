<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\Fixture;

class WithoutMethodOrStaticCall
{
    public function getData($arg)
    {
        return [];
    }

    public static function getData2($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        $data = [];
        if ($data === []) {

        } elseif ($data !== []) {

        }
    }

}
