<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRuleTest
 */
final class CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'autowire(), autoconfigure(), and public() are required in config service';

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
        $type = $scope->getType($node->var);
        if (! $type instanceof ObjectType) {
            return [];
        }

        $className = $type->getClassName();
        if (! is_a($className, ServicesConfigurator::class, true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
