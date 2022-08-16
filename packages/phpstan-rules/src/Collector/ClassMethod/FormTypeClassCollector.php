<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
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
        $methodName = $node->name->toString();
        if ($methodName !== 'configureOptions') {
            return null;
        }

        $stmts = $node->stmts;
        if (! is_array($stmts)) {
            return null;
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($stmts, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $methodCall->name instanceof Identifier) {
                continue;
            }

            $methodCallName = $methodCall->name->toString();
            if ($methodCallName === 'setDefaults') {
                return $this->resolveDataClassFromSetDefaults($methodCall, $scope);
            }

            if ($methodCallName === 'setDefault') {
                return $this->resolveDataClassFromSetDefault($methodCall, $scope);
            }
        }

        return null;
    }

    /**
     * @return string[]|null
     */
    private function resolveDataClassFromSetDefaults(MethodCall $methodCall, Scope $scope): ?array
    {
        /** @var ArrayItem[] $arrayItems */
        $arrayItems = $this->nodeFinder->findInstanceOf($methodCall->args, ArrayItem::class);

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

    /**
     * @return string[]|null
     */
    private function resolveDataClassFromSetDefault(MethodCall $methodCall, Scope $scope): ?array
    {
        $args = $methodCall->getArgs();
        if (count($args) !== 2) {
            return null;
        }

        $firstArgValue = $args[0]->value;
        if (! $firstArgValue instanceof String_) {
            return null;
        }

        if ($firstArgValue->value !== 'data_class') {
            return null;
        }

        $secondArgValue = $args[1]->value;
        $secondArgType = $scope->getType($secondArgValue);
        if (! $secondArgType instanceof ConstantStringType) {
            return null;
        }

        return [$secondArgType->getValue()];
    }
}
