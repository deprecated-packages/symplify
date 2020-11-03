<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;
use Symplify\PHPStanRules\Contract\ManyNodeRuleInterface;

abstract class AbstractSymplifyRule implements Rule, ManyNodeRuleInterface
{
    public function getShortClassName(Scope $scope): ?string
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return null;
        }

        return $this->resolveShortName($className);
    }

    public function getClassName(Scope $scope): ?string
    {
        if ($scope->isInTrait()) {
            $traitReflection = $scope->getTraitReflection();
            if ($traitReflection === null) {
                return null;
            }

            return $traitReflection->getName();
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return null;
        }

        return $classReflection->getName();
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipNode($node)) {
            return [];
        }

        return $this->process($node, $scope);
    }

    public function resolveCurrentClassName(Node $node): ?string
    {
        $class = $this->resolveCurrentClass($node);
        if ($class === null) {
            return null;
        }

        // anonymous  class
        if ($class->namespacedName === null) {
            return null;
        }

        return (string) $class->namespacedName;
    }

    public function resolveCurrentClass(Node $node): ?Class_
    {
        $class = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($class) {
            if ($class instanceof Class_) {
                return $class;
            }

            $class = $class->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }

    public function resolveCurrentClassMethod(Node $node): ?ClassMethod
    {
        $classMethod = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($classMethod) {
            if ($classMethod instanceof ClassMethod) {
                return $classMethod;
            }

            $classMethod = $classMethod->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }

    public function getFirstParentByType(Node $node, string $nodeClass): ?Node
    {
        $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($node) {
            if (is_a($node, $nodeClass, true) && $node instanceof Node) {
                return $node;
            }

            $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }

    private function shouldSkipNode(Node $node): bool
    {
        $nodeTypes = $this->getNodeTypes();
        foreach ($nodeTypes as $nodeType) {
            if (is_a($node, $nodeType, true)) {
                return false;
            }
        }

        return true;
    }

    private function resolveShortName(string $className): string
    {
        if (! Strings::contains($className, '\\')) {
            return $className;
        }

        return (string) Strings::after($className, '\\', -1);
    }
}
