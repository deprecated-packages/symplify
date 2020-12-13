<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
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
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Closure::class];
    }

    /**
     * @param Closure $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $params = $node->params;

        if (count($params) !== 1) {
            return [];
        }

        $param = $params[0];
        if (! $param->type instanceof FullyQualified) {
            return [];
        }

        $classNameParam = (string) $param->type;
        if (! is_a($classNameParam, ContainerConfigurator::class, true)) {
            return [];
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($node->stmts, MethodCall::class);

        // at least 2 methods: 1st is calling parameters(), 2nd calling set
        if (count($methodCalls) < 2) {
            return [];
        }

        $firstMethodName = $this->getMethodCallName($methodCalls[0]);
        $secondMethodName = $this->getMethodCallName($methodCalls[1]);

        if ($firstMethodName !== 'parameters' || $secondMethodName !== 'set') {
            return [];
        }

        if (! isset($methodCalls[2])) {
            return [];
        }

        return $this->validateDoubleSetParameter($methodCalls);
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

    /**
     * @param MethodCall[] $methodCalls
     * @return string[]
     */
    private function validateDoubleSetParameter(array $methodCalls): array
    {
        unset($methodCalls[0]);
        $values = [];

        foreach ($methodCalls as $methodCall) {
            $args = $methodCall->args;
            if (count($args) !== 2) {
                continue;
            }

            if (! $args[0]->value instanceof String_) {
                continue;
            }

            if (in_array($args[0]->value->value, $values, true)) {
                return [self::ERROR_MESSAGE];
            }

            $values[] = $args[0]->value->value;
        }

        return [];
    }
}
