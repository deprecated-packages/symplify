<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignVarUseLoopVar
{
    public function run()
    {
        foreach (self::BEFORE_TRAIT_TYPES as $type) {
            foreach ($class->stmts as $key => $classStmt) {
                if (! $classStmt instanceof $type) {
                    continue;
                }

                $class->stmts = $this->insertBefore($class->stmts, $traitUse, $key);

                return;
            }
        }

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

        foreach ($usedTraits as $usedTrait) {
            foreach ($this->traitsToRemove as $traitToRemove) {
                if ($this->isName($usedTrait, $traitToRemove)) {
                    $this->removeNode($usedTrait);
                    $this->classHasChanged = true;
                    continue 2;
                }
            }
        }

        foreach ($class->getMethods() as $classMethod) {
            foreach ($removedPropertyNames as $removedPropertyName) {
                // remove methods
                $setMethodName = 'set' . ucfirst($removedPropertyName);
                $getMethodName = 'get' . ucfirst($removedPropertyName);

                if ($this->isNames($classMethod, [$setMethodName, $getMethodName])) {
                    continue;
                }

                $this->removeNode($classMethod);
            }
        }

        foreach ($parentNestingBreakTypes as $parentNestingBreakType) {
            if (! is_a($node, $parentNestingBreakType, true)) {
                continue;
            }

            $this->isBreakingNodeFoundFirst = true;
            return true;
        }
    }
}
