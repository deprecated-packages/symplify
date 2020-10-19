<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\Fixture;

class WithStaticCall
{
    public static function getData($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        foreach (self::getData($arg) as $key => $item) {

        }
    }

}