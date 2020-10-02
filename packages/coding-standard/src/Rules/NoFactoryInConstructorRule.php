<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ThisType;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\NoFactoryInConstructorRuleTest
 */
final class NoFactoryInConstructorRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use factory/method call in constructor, put factory in config and get service with dependency injection';

    /**
     * @var string[]
     */
    private const ALLOWED_TYPES = [ParameterProvider::class, ParameterBagInterface::class];

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
        $parent = $node->getAttribute('parent');
        if ($parent instanceof ArrayDimFetch) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if ($callerType instanceof ThisType) {
            return [];
        }

        if (! $callerType instanceof TypeWithClassName) {
            return [];
        }

        foreach (self::ALLOWED_TYPES as $allowedType) {
            if (is_a($callerType->getClassName(), $allowedType, true)) {
                return [];
            }
        }

        return [self::ERROR_MESSAGE];
    }

    private function isInConstructMethod(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === '__construct';
    }
}
