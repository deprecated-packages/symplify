<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;

final class ArrayAnalyzer
{
    public function isArrayWithStringKey(Array_ $array): bool
    {
        foreach ($array->items as $arrayItem) {
            if ($arrayItem === null) {
                continue;
            }

            /** @var ArrayItem $arrayItem */
            if ($arrayItem->key === null) {
                continue;
            }

            if (! $arrayItem->key instanceof String_) {
                continue;
            }

            return true;
        }

        return false;
    }
}
