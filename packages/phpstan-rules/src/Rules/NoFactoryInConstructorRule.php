<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Doctrine\ORM\EntityManagerInterface;
use Jean85\PrettyVersions;
use Jean85\Version;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PHPStanRules\Reflection\MethodNodeAnalyser;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\NoFactoryInConstructorRuleTest
 */
final class NoFactoryInConstructorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use factory/method call in constructor. Put factory in config and get service with dependency injection';

    /**
     * @var string[]
     */
    private const ALLOWED_TYPES = [
        ParameterProvider::class,
        ParameterBagInterface::class,
        EntityManagerInterface::class,
        PrettyVersions::class,
        Version::class,
    ];

    /**
     * @var string[]
     */
    private const SKIP_CLASS_NAMES = [
        // to resolve extra values
        '*\ValueObject\*',
    ];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var MethodNodeAnalyser
     */
    private $methodNodeAnalyser;

    public function __construct(
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        MethodNodeAnalyser $methodNodeAnalyser
    ) {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->methodNodeAnalyser = $methodNodeAnalyser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->methodNodeAnalyser->isInConstructor($scope)) {
            return [];
        }

        if (! $node->var instanceof Variable) {
            return [];
        }

        // just assign
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if ($parent instanceof ArrayDimFetch) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if ($callerType instanceof ThisType) {
            return [];
        }

        if ($this->isAllowedType($callerType)) {
            return [];
        }

        if ($this->isInAllowedClass($scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $someDependency;

    public function __construct(SomeFactory $factory)
    {
        $this->someDependency = $factory->build();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $someDependency;

    public function __construct(SomeDependency $someDependency)
    {
        $this->someDependency = $someDependency;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isAllowedType(Type $type): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        return $this->arrayStringAndFnMatcher->isMatch($type->getClassName(), self::ALLOWED_TYPES);
    }

    private function isInAllowedClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $className = $classReflection->getName();

        return $this->arrayStringAndFnMatcher->isMatch($className, self::SKIP_CLASS_NAMES);
    }
}
