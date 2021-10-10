<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class ParametersArrayAnalyzer
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver
    ) {
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
