<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use Nette\Utils\Json;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\Exception\ShouldNotHappenException;
use Symplify\PhpConfigPrinter\PhpParser\NodeFactory\ConfiguratorClosureNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableMethodName;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ContainerConfiguratorReturnClosureFactory
{
    /**
     * @param CaseConverterInterface[] $caseConverters
     */
    public function __construct(
        private readonly ConfiguratorClosureNodeFactory $configuratorClosureNodeFactory,
        private readonly array $caseConverters,
        private readonly ContainerNestedNodesFactory $containerNestedNodesFactory
    ) {
    }

    /**
     * @param array<string, mixed[]> $arrayData
     */
    public function createFromYamlArray(
        array $arrayData,
        string $containerConfiguratorClass = 'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator'
    ): Return_ {
        $stmts = $this->createClosureStmts($arrayData);

        $closure = $this->configuratorClosureNodeFactory->createContainerClosureFromStmts(
            $stmts,
            $containerConfiguratorClass
        );

        return new Return_($closure);
    }

    /**
     * @param mixed[] $yamlData
     * @return Stmt[]
     */
    private function createClosureStmts(array $yamlData): array
    {
        $yamlData = array_filter($yamlData);
        return $this->createStmtsFromCaseConverters($yamlData);
    }

    /**
     * @param array<string, mixed[]> $yamlData
     * @return Stmt[]
     */
    private function createStmtsFromCaseConverters(array $yamlData): array
    {
        $stmts = [];

        foreach ($yamlData as $key => $values) {
            // keys can be int, but we need string
            if (! is_string($key)) {
                $key = (string) $key;
            }

            $stmts = $this->createInitializeStmt($key, $stmts);

            foreach ($values as $nestedKey => $nestedValues) {
                $nestedNodes = $this->processNestedNodes($key, $nestedKey, $nestedValues);

                if ($nestedNodes !== []) {
                    $stmts = array_merge($stmts, $nestedNodes);
                    continue;
                }

                $expression = $this->resolveStmt($key, $nestedKey, $nestedValues);
                if (! $expression instanceof Expression) {
                    continue;
                }

                $lastNode = end($stmts);
                $node = $this->resolveExpressionWhenAtEnv($expression, $key, $lastNode);
                if ($node !== null) {
                    $stmts[] = $node;
                }
            }
        }

        return $stmts;
    }

    /**
     * @return Expression[]|mixed[]
     */
    private function processNestedNodes(string $key, int|string $nestedKey, mixed $nestedValues): array
    {
        if (is_array($nestedValues)) {
            return $this->containerNestedNodesFactory->createFromValues($nestedValues, $key, $nestedKey);
        }

        return [];
    }

    private function resolveExpressionWhenAtEnv(
        Expression $expression,
        string $key,
        Expression|If_|bool $lastNode
    ): Expression|If_|null {
        $explodeAt = explode('@', $key);
        if (str_starts_with($key, 'when@') && count($explodeAt) === 2) {
            $variable = new Variable(VariableName::CONTAINER_CONFIGURATOR);

            $expr = $expression->expr;
            if (! $expr instanceof MethodCall) {
                throw new ShouldNotHappenException();
            }

            $args = $expr->getArgs();

            if (! isset($args[1]) || ! $args[1]->value instanceof Array_ || ! isset($args[1]->value->items[0])
                || ! $args[1]->value->items[0] instanceof ArrayItem || $args[1]->value->items[0]->key === null) {
                throw new ShouldNotHappenException();
            }

            $newExpression = new Expression(
                new MethodCall(
                    $variable,
                    'extension',
                    [new Arg($args[1]->value->items[0]->key), new Arg($args[1]->value->items[0]->value)]
                )
            );

            $environmentString = new String_($explodeAt[1]);
            $envMethodCall = new MethodCall($variable, 'env');

            $identical = new Identical($envMethodCall, $environmentString);

            if ($lastNode instanceof If_ && $this->isSameCond($lastNode->cond, $identical)) {
                $lastNode->stmts[] = $newExpression;
                return null;
            }

            $if = new If_($identical);
            $if->stmts = [$newExpression];

            return $if;
        }

        return $expression;
    }

    private function isSameCond(Expr $expr, Identical $identical): bool
    {
        if ($expr instanceof Identical) {
            $val1 = Json::encode($expr);
            $val2 = Json::encode($identical);
            return $val1 === $val2;
        }

        return false;
    }

    /**
     * @param VariableMethodName::* $variableMethodName
     */
    private function createInitializeAssign(string $variableMethodName): Expression
    {
        $servicesVariable = new Variable($variableMethodName);
        $containerConfiguratorVariable = new Variable(VariableName::CONTAINER_CONFIGURATOR);

        $assign = new Assign($servicesVariable, new MethodCall($containerConfiguratorVariable, $variableMethodName));

        return new Expression($assign);
    }

    /**
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    private function createInitializeStmt(string $key, array $stmts): array
    {
        if ($key === YamlKey::SERVICES) {
            $stmts[] = $this->createInitializeAssign(VariableMethodName::SERVICES);
            return $stmts;
        }

        if ($key === YamlKey::PARAMETERS) {
            $stmts[] = $this->createInitializeAssign(VariableMethodName::PARAMETERS);
            return $stmts;
        }

        return $stmts;
    }

    private function resolveStmt(string $key, int | string $nestedKey, mixed $nestedValues): ?Stmt
    {
        foreach ($this->caseConverters as $caseConverter) {
            if (! $caseConverter->match($key, $nestedKey, $nestedValues)) {
                continue;
            }

            /** @var string $nestedKey */
            return $caseConverter->convertToMethodCallStmt($nestedKey, $nestedValues);
        }

        return null;
    }
}
