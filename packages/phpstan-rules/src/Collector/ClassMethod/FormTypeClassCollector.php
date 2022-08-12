<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\Constant\ConstantStringType;

/**
 * @implements Collector<ClassMethod, string[]>
 */
final class FormTypeClassCollector implements Collector
{
    public function __construct(
        private NodeFinder $nodeFinder,
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($node->isMagic()) {
            return null;
        }

        $methodName = $node->name->toString();
        if ($methodName !== 'configureOptions') {
            return null;
        }

        /** @var ArrayItem[] $arrayItems */
        $arrayItems = $this->nodeFinder->findInstanceOf((array) $node->stmts, ArrayItem::class);

        foreach ($arrayItems as $arrayItem) {
            if (! $arrayItem->key instanceof String_) {
                continue;
            }

            $arrayItemKey = $arrayItem->key->value;
            if ($arrayItemKey !== 'data_class') {
                continue;
            }

            $arrayValueType = $scope->getType($arrayItem->value);
            if (! $arrayValueType instanceof ConstantStringType) {
                continue;
            }

            return [$arrayValueType->getValue()];
        }

        return null;
    }
}
