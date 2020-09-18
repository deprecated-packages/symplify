<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\NoScalarAndArrayConstructorParameterRuleTest
 */
final class NoScalarAndArrayConstructorParameterRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use scalar and array in constructor parameter';

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

        if ($this->isValueObjectNamespace($parent)) {
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
        foreach ($parameters as $parameter) {
            /** @var Identifier|Name|UnionType|NullableType|null $type */
            $type = $parameter->type;

            // for null, UnionType and NullableType
            if ($type === null || ! method_exists($type, 'toString')) {
                continue;
            }

            /** @var Identifier|Name $type */
            $typeName = $type->toString();
            if (in_array($typeName, ['string', 'int', 'float', 'bool', 'array'], true)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    private function isValueObjectNamespace(Class_ $class): bool
    {
        $namespacedName = $class->namespacedName->toString();
        /** @var Identifier $name */
        $name = $class->name;
        /** @var string $className */
        $className = $name->toString();
        $findValueObjectNamespace = '\\ValueObject\\' . $className;
        $positionValueObjectNamespace = strpos($namespacedName, $findValueObjectNamespace);

        if ($positionValueObjectNamespace === false) {
            return false;
        }

        return $positionValueObjectNamespace === strlen($namespacedName) - strlen($findValueObjectNamespace);
    }
}
