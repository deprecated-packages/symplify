<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\Naming\ClassNameAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\ConstructorDefinedPropertyNodeAnalyzer;
use Symplify\PHPStanRules\TypeAnalyzer\ObjectTypeAnalyzer;
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
    private const ALLOWED_PROPERTY_TYPES = [
        'PhpParser\NodeVisitor',
        'Symplify\SimplePhpDocParser\Contract\PhpDocNodeVisitorInterface',
    ];

    /**
     * @var array<class-string>
     */
    private const ALLOWED_CALLER_TYPES = [
        'Symplify\PackageBuilder\Reflection\PrivatesCaller',
        'Symplify\PackageBuilder\Reflection\PrivatesAccessor',
    ];

    /**
     * @var array<class-string>
     */
    private const ALLOWED_CLASS_TYPES = [
        'Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface',
        'Symfony\Component\HttpKernel\KernelInterface',
    ];

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
        $parent = $propertyFetch->getAttribute(AttributeKey::PARENT);
        if (! $parent instanceof Arg) {
            return true;
        }

        $parentParent = $parent->getAttribute(AttributeKey::PARENT);
        if ($this->isAllowedCallerType($scope, $parentParent)) {
            return true;
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

    private function isAllowedCallerType(Scope $scope, Expr $expr): bool
    {
        if (! $expr instanceof MethodCall) {
            return false;
        }

        $callerType = $scope->getType($expr->var);

        foreach (self::ALLOWED_CALLER_TYPES as $allowedCallerType) {
            $privatesCallerObjectType = new ObjectType($allowedCallerType);
            if ($privatesCallerObjectType->isSuperTypeOf($callerType)->yes()) {
                return true;
            }
        }

        return false;
    }
}
