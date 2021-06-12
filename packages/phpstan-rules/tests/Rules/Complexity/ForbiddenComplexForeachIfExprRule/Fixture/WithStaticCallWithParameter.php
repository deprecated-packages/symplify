<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\Fixture;

class WithStaticCallWithParameter
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
