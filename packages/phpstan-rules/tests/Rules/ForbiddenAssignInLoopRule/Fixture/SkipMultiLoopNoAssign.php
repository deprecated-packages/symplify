<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipMultiLoopNoAssign
{
    public function run()
    {
        foreach ($funcCall->args as $position => $arg) {
            if (! $arg->value instanceof Array_) {
                continue;
            }

            foreach ($arg->value->items as $arrayKey => $item) {
                if (! $item instanceof ArrayItem) {
                    continue;
                }

                $value = $this->getValue($item->value);
                if ($scope->hasVariableType($value)->yes()) {
                    continue;
                }

                unset($arg->value->items[$arrayKey]);
            }

            if ($arg->value->items === []) {
                // Drops empty array from `compact()` arguments.
                unset($funcCall->args[$position]);
            }
        }
    }
}