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
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\CodingStandard\ValueObject\MethodName;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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

    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher)
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
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
        if (! $this->isInConstructMethod($scope)) {
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

    private function isInConstructMethod(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === MethodName::CONSTRUCTOR;
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
        if ($classReflection === null) {
            return false;
        }

        $className = $classReflection->getName();

        return $this->arrayStringAndFnMatcher->isMatch($className, self::SKIP_CLASS_NAMES);
    }
}
