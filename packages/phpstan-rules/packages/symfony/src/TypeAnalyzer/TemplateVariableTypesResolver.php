<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\TypeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class TemplateVariableTypesResolver
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    /**
     * @return array<string, Type>
     */
    public function resolveArray(Array_ $array, Scope $scope): array
    {
        $variableNamesToTypes = [];

        foreach ($array->items as $arrayItem) {
            if (! $arrayItem instanceof ArrayItem) {
                continue;
            }

            if ($arrayItem->key === null) {
                continue;
            }

            $keyName = $this->nodeValueResolver->resolve($arrayItem->key, $scope->getFile());
            if (! is_string($keyName)) {
                continue;
            }

            $variableNamesToTypes[$keyName] = $scope->getType($arrayItem->value);
        }

        return $variableNamesToTypes;
    }
}
