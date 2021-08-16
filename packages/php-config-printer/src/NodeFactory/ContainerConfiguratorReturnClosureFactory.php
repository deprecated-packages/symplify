<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
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
        private ConfiguratorClosureNodeFactory $configuratorClosureNodeFactory,
        private array $caseConverters,
        private ContainerNestedNodesFactory $containerNestedNodesFactory
    ) {
    }

    /**
     * @param array<string, mixed[]> $arrayData
     */
    public function createFromYamlArray(array $arrayData): Return_
    {
        $stmts = $this->createClosureStmts($arrayData);
        $closure = $this->configuratorClosureNodeFactory->createContainerClosureFromStmts($stmts);

        return new Return_($closure);
    }

    /**
     * @return Stmt[]
     */
    private function createClosureStmts(array $yamlData): array
    {
        $yamlData = array_filter($yamlData);
        return $this->createNodesFromCaseConverters($yamlData);
    }

    /**
     * @param array<string, mixed[]> $yamlData
     * @return Stmt[]
     */
    private function createNodesFromCaseConverters(array $yamlData): array
    {
        $nodes = [];

        foreach ($yamlData as $key => $values) {
            $nodes = $this->createInitializeNode($key, $nodes);

            foreach ($values as $nestedKey => $nestedValues) {
                $nestedNodes = [];

                if (is_array($nestedValues)) {
                    $nestedNodes = $this->containerNestedNodesFactory->createFromValues(
                        $nestedValues,
                        $key,
                        $nestedKey
                    );
                }

                if ($nestedNodes !== []) {
                    $nodes = array_merge($nodes, $nestedNodes);
                    continue;
                }

                $expression = $this->resolveExpression($key, $nestedKey, $nestedValues);
                if (! $expression instanceof Expression) {
                    continue;
                }

                $nodes[] = $expression;
            }
        }

        return $nodes;
    }

    private function createInitializeAssign(string $variableMethodName): Expression
    {
        $servicesVariable = new Variable($variableMethodName);
        $containerConfiguratorVariable = new Variable(VariableName::CONTAINER_CONFIGURATOR);

        $assign = new Assign($servicesVariable, new MethodCall($containerConfiguratorVariable, $variableMethodName));

        return new Expression($assign);
    }

    /**
     * @return mixed[]
     */
    private function createInitializeNode(string $key, array $nodes): array
    {
        if ($key === YamlKey::SERVICES) {
            $nodes[] = $this->createInitializeAssign(VariableMethodName::SERVICES);
            return $nodes;
        }

        if ($key === YamlKey::PARAMETERS) {
            $nodes[] = $this->createInitializeAssign(VariableMethodName::PARAMETERS);
            return $nodes;
        }

        return $nodes;
    }

    /**
     * @param mixed|mixed[] $nestedValues
     */
    private function resolveExpression(string $key, int | string $nestedKey, $nestedValues): ?Expression
    {
        foreach ($this->caseConverters as $caseConverter) {
            if (! $caseConverter->match($key, $nestedKey, $nestedValues)) {
                continue;
            }

            /** @var string $nestedKey */
            return $caseConverter->convertToMethodCall($nestedKey, $nestedValues);
        }

        return null;
    }
}
