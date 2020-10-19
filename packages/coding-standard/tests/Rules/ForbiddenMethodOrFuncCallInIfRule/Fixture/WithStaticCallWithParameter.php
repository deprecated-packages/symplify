<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule\Fixture;

class WithStaticCall
{
    public static function getData($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        if (self::getData($arg) === []) {

        } elseif (self::getData($arg) !== []) {

        }
    }

}