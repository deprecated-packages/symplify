<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use ReflectionClass;
use Symplify\PHPStanRules\Contract\ManyNodeRuleInterface;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

abstract class AbstractSymplifyRule implements Rule, ManyNodeRuleInterface, DocumentedRuleInterface
{
    public function getShortClassName(Scope $scope): ?string
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return null;
        }

        return $this->resolveShortName($className);
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

    protected function resolveCurrentClass(Node $node): ?Class_
    {
        if ($node instanceof Class_) {
            return $node;
        }

        $class = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($class) {
            if ($class instanceof Class_) {
                return $class;
            }

            $class = $class->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }

    protected function getClassName(Scope $scope, ?Node $node = null): ?string
    {
        if ($node instanceof ClassLike) {
            return $this->resolveClassLikeName($node);
        }

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

    protected function isInAbstractClass(Node $node): bool
    {
        $class = $this->resolveCurrentClass($node);
        if ($class === null) {
            return false;
        }

        return $class->isAbstract();
    }

    protected function isInDirectoryNamed(Scope $scope, string $directoryName): bool
    {
        return Strings::contains($scope->getFile(), DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR);
    }

    protected function containsNamespace(Namespace_ $namespace, string $part): bool
    {
        if ($namespace->name === null) {
            return false;
        }

        return in_array($part, $namespace->name->parts, true);
    }

    protected function doesMethodExistInTraits(Class_ $class, string $methodName): bool
    {
        /** @var Identifier $name */
        $name = $class->namespacedName;
        $usedTraits = class_uses($name->toString());

        foreach ($usedTraits as $trait) {
            $reflectionClass = new ReflectionClass($trait);
            if ($reflectionClass->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    protected function isInClassMethodNamed(Scope $scope, string $methodName): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === $methodName;
    }

    private function resolveClassLikeName(ClassLike $classLike): ?string
    {
        // anonymous  class
        if ($classLike->namespacedName === null) {
            return null;
        }

        return (string) $classLike->namespacedName;
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
