<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDoubleSetParameterRule\PreventDoubleSetParameterRuleTest
 */
final class PreventDoubleSetParameterRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Set param value is duplicated, use unique value instead';

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

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1]);
    $parameters->set('some_param', [2]);
};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1]);
    $parameters->set('some_param_2', [2]);
};
CODE_SAMPLE
            ),
        ]);
    }
}
