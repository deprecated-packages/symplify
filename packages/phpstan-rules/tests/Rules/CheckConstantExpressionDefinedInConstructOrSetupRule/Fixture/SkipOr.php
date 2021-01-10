<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\List_;

final class SkipOr
{
    public function run(ArrayItem $arrayItem)
    {
        // Check if the item is a nested list/nested array destructuring
        $isNested = $arrayItem->value instanceof List_ || $arrayItem->value instanceof Array_;
    }
}
