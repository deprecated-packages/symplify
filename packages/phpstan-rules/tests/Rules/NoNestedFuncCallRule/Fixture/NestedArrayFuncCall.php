<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class NestedArrayFuncCall
{
    public function run(array $expectationMocks)
    {
        return array_map(static function (ExpectationMock $expectationMock): Arg {
            $arrayItems = array_map(static function (?Expr $expr): ArrayItem {
                return new ArrayItem($expr ?: new ConstFetch(new Name('null')));
            }, $expectationMock->getWithArguments());
            return new Arg(new Array_($arrayItems));
        }, $expectationMocks);
    }
}
