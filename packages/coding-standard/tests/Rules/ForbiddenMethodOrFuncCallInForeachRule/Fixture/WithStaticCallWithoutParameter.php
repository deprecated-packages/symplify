<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInForeachRule\Fixture;

class WithStaticCallWithoutParameter
{
    public static function getData()
    {
        return [];
    }

    public function execute()
    {
        foreach (self::getData() as $key => $item) {

        }
    }

}