<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
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
     * @var string
     * @see https://regex101.com/r/HDOhtp/3
     */
    private const REGEX_VALUE_OBJECT_NAMESPACE_PLACEHOLDER = '#\\\\?ValueObject\\\\([A-Za-z]+\\\\){0,}(?=%s$)#';

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

        $methodName = (string) $node->name;
        if (! $node->isMagic() || $methodName !== '__construct') {
            return [];
        }

        $parameters = $node->getParams();
        foreach ($parameters as $parameter) {
            /** @var Identifier|Name|UnionType|NullableType|null $type */
            $type = $parameter->type;

            if ($type === null) {
                continue;
            }

            $possibleTypes = $this->getPossibleTypes($type);
            if ($this->isScalarOrArray($possibleTypes)) {
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
        $pattern = sprintf(self::REGEX_VALUE_OBJECT_NAMESPACE_PLACEHOLDER, $className);

        return Strings::match($namespacedName, $pattern) !== null;
    }

    /**
     * @return mixed[]
     */
    private function getPossibleTypes($type): array
    {
        if ($type instanceof NullableType) {
            return [$type->type];
        }

        if ($type instanceof UnionType) {
            return $type->types;
        }

        /** @var Identifier|Name $type */
        return [$type];
    }

    private function isScalarOrArray(array $types): bool
    {
        foreach ($types as $type) {
            /** @var Identifier|Name $type */
            $typeName = $type->toString();
            if (in_array($typeName, ['string', 'int', 'float', 'bool', 'array'], true)) {
                return true;
            }
        }

        return false;
    }
}
