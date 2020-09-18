<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\NoScalarAndArrayConstructorParameterRule
 */
final class NoScalarAndArrayConstructorParameterRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use scalar in constructor parameter';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute('parent');
        if (! $parent instanceof Class_) {
            return [];
        }

        $namespacedName = $parent->namespacedName->toString();
        $name = $parent->name->toString();
        $findValueObjectNamespace = '\\ValueObject\\' . $name;

        if (strpos($namespacedName, $findValueObjectNamespace) === strlen($findValueObjectNamespace) - $find) {
            return [];
        }

        if (! $node->isMagic()) {
            return [];
        }

        $methodName = (string) $node->name;
        if ($methodName !== '__construct') {
            return [];
        }

        $parameters = $node->getParams();
        if ($parameters === []) {
            return [];
        }

        foreach ($parameters as $parameter) {
            $type = $parameter->type;

            if ($type === null) {
                continue;
            }

            $typeName = $type->toString();
            if (in_array($typeName, ['string', 'int', 'float', 'bool', 'array'], true)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
