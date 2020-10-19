<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule\Fixture;

class WithStaticCallWithoutParameter
{
    public static function getData()
    {
        return [];
    }

    public function execute()
    {
        if (self::getData() === []) {

        } elseif (self::getData() !== []) {

        }
    }

}