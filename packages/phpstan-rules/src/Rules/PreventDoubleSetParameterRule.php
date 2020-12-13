<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use PhpParser\Node\Identifier;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\NodeFinder;
use PhpParser\Node\Name\FullyQualified;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDoubleSetParameterRule\PreventDoubleSetParameterRuleTest
 */
final class PreventDoubleSetParameterRule extends AbstractSymplifyRule
{
    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var Standard
     */
    private $printerStandard;

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Set param value is duplicated, use unique value instead';

    public function __construct(NodeFinder $nodeFinder, Standard $printerStandard)
    {
        $this->nodeFinder = $nodeFinder;
        $this->printerStandard = $printerStandard;
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

        $firstMethodName = (string) $methodCalls[0]->name;
        $secondMethodName = (string) $methodCalls[1]->name;

        if ($firstMethodName !== 'parameters' || $secondMethodName !== 'set') {
            return [];
        }

        return [];


        /*$type = $scope->getType($node->var);
        if (! $type instanceof ObjectType) {
            return [];
        }

        $className = $type->getClassName();
        if (! is_a($className, ParametersConfigurator::class, true)) {
            return [];
        }

        $methodIdentifier = $node->name;
        if ($methodIdentifier->toString() !== 'set') {
            return [];
        }

        static $values = [];
        $args = $node->args;

        foreach ($values as $value) {
            if ($this->areNodesEqual($value, $args[0]->value)) {
                return [self::ERROR_MESSAGE];
            }
        }

        $values[] = $args[0]->value;
        return [];*/
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

    private function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        return $this->printerStandard->prettyPrint([$firstNode]) === $this->printerStandard->prettyPrint([$secondNode]);
    }
}
