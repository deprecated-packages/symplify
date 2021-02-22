<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentMethodAnalyser;
use Symplify\PHPStanRules\TypeAnalyzer\ClassMethodTypeAnalyzer;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\NoProtectedElementInFinalClassRuleTest
 */
final class NoProtectedElementInFinalClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of protected element in final class use private element or contract method';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    /**
     * @var ClassMethodTypeAnalyzer
     */
    private $classMethodTypeAnalyzer;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(
        ParentMethodAnalyser $parentMethodAnalyser,
        ClassMethodTypeAnalyzer $classMethodTypeAnalyzer,
        SimpleNameResolver $simpleNameResolver,
        ReflectionProvider $reflectionProvider
    ) {
        $this->parentMethodAnalyser = $parentMethodAnalyser;
        $this->classMethodTypeAnalyzer = $classMethodTypeAnalyzer;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class];
    }

    /**
     * @param Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Class_) {
            return [];
        }

        if (! $parent->isFinal()) {
            return [];
        }

        if (! $node->isProtected()) {
            return [];
        }

        if ($node instanceof ClassMethod) {
            return $this->processClassMethod($node, $parent, $scope);
        }

        return $this->processProperty($parent, $node);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    protected function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private function run()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isPropertyExistInParentClass(Name $name, string $propertyName): bool
    {
        $classReflection = $this->reflectionProvider->getClass((string) $name);
        return $classReflection->hasProperty($propertyName);
    }

    /**
     * @return string[]
     */
    private function processClassMethod(ClassMethod $classMethod, Class_ $class, Scope $scope): array
    {
        // is Symfony Kernel required magic method?
        if ($this->isSymfonyMicroKernelRequired($classMethod, $scope)) {
            return [];
        }

        $methodName = (string) $classMethod->name;
        if ($this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    private function processProperty(Class_ $class, Property $property): array
    {
        $extends = $class->extends;
        if (! $extends instanceof Name) {
            return [self::ERROR_MESSAGE];
        }

        /** @var string $propertyName */
        $propertyName = $this->simpleNameResolver->getName($property);
        if ($this->isPropertyExistInParentClass($extends, $propertyName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isSymfonyMicroKernelRequired(ClassMethod $classMethod, Scope $scope): bool
    {
        return $this->classMethodTypeAnalyzer->isClassMethodOfNamesAndType(
            $classMethod,
            $scope,
            ['configureRoutes', 'configureContainer'],
            MicroKernelTrait::class
        );
    }
}
