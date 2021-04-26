<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PHPStanRules\Naming\ClassNameAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\ConstructorDefinedPropertyNodeAnalyzer;
use Symplify\PHPStanRules\TypeAnalyzer\ObjectTypeAnalyzer;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\NoDependencyJugglingRuleTest
 */
final class NoDependencyJugglingRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use dependency injection instead of dependency juggling';

    /**
     * @var array<class-string<NodeVisitor>>
     */
    private const ALLOWED_PROPERTY_TYPES = ['PhpParser\NodeVisitor'];

    /**
     * @var array<class-string<CompilerPassInterface>>
     */
    private const ALLOWED_CLASS_TYPES = ['Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface'];

    /**
     * @var ConstructorDefinedPropertyNodeAnalyzer
     */
    private $constructorDefinedPropertyNodeAnalyzer;

    /**
     * @var ClassNameAnalyzer
     */
    private $classNameAnalyzer;

    /**
     * @var ObjectTypeAnalyzer
     */
    private $objectTypeAnalyzer;

    public function __construct(
        ConstructorDefinedPropertyNodeAnalyzer $constructorDefinedPropertyNodeAnalyzer,
        ClassNameAnalyzer $classNameAnalyzer,
        ObjectTypeAnalyzer $objectTypeAnalyzer
    ) {
        $this->constructorDefinedPropertyNodeAnalyzer = $constructorDefinedPropertyNodeAnalyzer;
        $this->classNameAnalyzer = $classNameAnalyzer;
        $this->objectTypeAnalyzer = $objectTypeAnalyzer;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param PropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipPropertyFetch($node, $scope)) {
            return [];
        }

        if (! $this->constructorDefinedPropertyNodeAnalyzer->isLocalPropertyDefinedInConstructor($node, $scope)) {
            return [];
        }

        // is factory class/method?
        if ($this->classNameAnalyzer->isFactoryClassOrMethod($scope)) {
            return [];
        }

        if ($this->classNameAnalyzer->isValueObjectClass($scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
public function __construct($service)
{
    $this->service = $service;
}

public function run($someObject)
{
    return $someObject->someMethod($this->service);
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
public function run($someObject)
{
    return $someObject->someMethod();
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipPropertyFetch(PropertyFetch $propertyFetch, Scope $scope): bool
    {
        $parent = $propertyFetch->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Arg) {
            return true;
        }

        $parentParent = $parent->getAttribute(PHPStanAttributeKey::PARENT);
        if ($parentParent instanceof MethodCall) {
            // special allowed case
            $callerType = $scope->getType($parentParent->var);
            $privatesCallerObjectType = new ObjectType(PrivatesCaller::class);
            if ($privatesCallerObjectType->isSuperTypeOf($callerType)->yes()) {
                return true;
            }
        }

        return $this->isAllowedType($scope, $propertyFetch);
    }

    private function isAllowedType(Scope $scope, PropertyFetch $propertyFetch): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        $classObjectType = new ObjectType($classReflection->getName());
        if ($this->objectTypeAnalyzer->isObjectOrUnionOfObjectTypes($classObjectType, self::ALLOWED_CLASS_TYPES)) {
            return true;
        }

        $propertyFetchType = $scope->getType($propertyFetch);
        if (! $propertyFetchType instanceof TypeWithClassName) {
            return true;
        }

        return $this->objectTypeAnalyzer->isObjectOrUnionOfObjectTypes(
            $propertyFetchType,
            self::ALLOWED_PROPERTY_TYPES
        );
    }
}
