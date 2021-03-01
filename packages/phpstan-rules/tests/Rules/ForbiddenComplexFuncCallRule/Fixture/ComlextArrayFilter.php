<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexFuncCallRule\Fixture;

final class ComlextArrayFilter
{
    public function run(array $items)
    {
        return array_filter($items, function ($item) {
            if (mt_rand(1, 0)) {
                return true;
            }

            return $item;
        });
    }
}
