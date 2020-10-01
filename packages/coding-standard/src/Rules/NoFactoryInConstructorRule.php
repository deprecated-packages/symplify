<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
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
    public const ERROR_MESSAGE = 'Do not use factory in constructor';

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
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return [];
        }

        if ($reflectionFunction->getName() !== '__construct') {
            return [];
        }

        if (! $node->var instanceof Variable) {
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
}
