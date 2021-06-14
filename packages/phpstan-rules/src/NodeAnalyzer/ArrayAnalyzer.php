<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class ArrayAnalyzer
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver
    ) {
    }

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

    /**
     * @return string[]
     */
    public function resolveStringKeys(Array_ $array, Scope $scope): array
    {
        $stringKeyNames = [];

        foreach ($array->items as $arrayItem) {
            if (! $arrayItem instanceof ArrayItem) {
                continue;
            }

            if ($arrayItem->key === null) {
                continue;
            }

            $keyValue = $this->nodeValueResolver->resolve($arrayItem->key, $scope->getFile());
            if (! is_string($keyValue)) {
                continue;
            }

            $stringKeyNames[] = $keyValue;
        }

        return $stringKeyNames;
    }
}
