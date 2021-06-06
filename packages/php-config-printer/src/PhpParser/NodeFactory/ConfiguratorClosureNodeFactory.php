<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\PhpParser\NodeFactory;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class ConfiguratorClosureNodeFactory
{
    /**
     * @param Stmt[] $stmts
     */
    public function createContainerClosureFromStmts(array $stmts): Closure
    {
        $param = $this->createContainerConfiguratorParam();
        return $this->createClosureFromParamAndStmts($param, $stmts);
    }

    /**
     * @param Stmt[] $stmts
     */
    public function createRoutingClosureFromStmts(array $stmts): Closure
    {
        $param = $this->createRoutingConfiguratorParam();
        return $this->createClosureFromParamAndStmts($param, $stmts);
    }

    private function createContainerConfiguratorParam(): Param
    {
        $containerConfiguratorVariable = new Variable(VariableName::CONTAINER_CONFIGURATOR);

        return new Param($containerConfiguratorVariable, null, new FullyQualified(ContainerConfigurator::class));
    }

    private function createRoutingConfiguratorParam(): Param
    {
        $containerConfiguratorVariable = new Variable(VariableName::ROUTING_CONFIGURATOR);
        return new Param($containerConfiguratorVariable, null, new FullyQualified(RoutingConfigurator::class));
    }

    /**
     * @param Stmt[] $stmts
     */
    private function createClosureFromParamAndStmts(Param $param, array $stmts): Closure
    {
        $stmts = $this->mergeStmtsFromSameClosure($stmts);

        $closure = new Closure([
            'params' => [$param],
            'stmts' => $stmts,
            'static' => true,
        ]);

        // is PHP 7.1? → add "void" return type
        if (version_compare(PHP_VERSION, '7.1.0') >= 0) {
            $closure->returnType = new Identifier('void');
        }

        return $closure;
    }

    /**
     * To avoid multiple arrays for the same extension
     *
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    private function mergeStmtsFromSameClosure(array $stmts): array
    {
        $extensionNodes = [];

        foreach ($stmts as $stmtKey => $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            $stmt = $stmt->expr;
            if (! $stmt instanceof MethodCall) {
                continue;
            }

            if ($stmt->name instanceof Expr) {
                continue;
            }

            if ((string) $stmt->name !== 'extension') {
                continue;
            }

            $firstArgValue = $stmt->args[0]->value;
            if (! $firstArgValue instanceof String_) {
                continue;
            }

            $extensionName = $firstArgValue->value;
            $extensionNodes[$extensionName][] = [
                $stmtKey => $stmt->args[1]->value,
            ];
        }

        if ($extensionNodes === []) {
            return $stmts;
        }

        return $this->replaceArrayArgWithMergedArrayItems($extensionNodes, $stmts);
    }

    /**
     * @param Expr[][][] $extensionNodes
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    private function replaceArrayArgWithMergedArrayItems(array $extensionNodes, array $stmts): array
    {
        foreach ($extensionNodes as $extensionStmts) {
            if (count($extensionStmts) === 1) {
                continue;
            }

            $firstStmtKey = $this->resolveFirstStmtKey($extensionStmts);
            $stmtKeysToRemove = $this->resolveStmtKeysToRemove($extensionStmts);
            $newArrayItems = $this->resolveMergedArrayItems($extensionStmts);

            foreach ($stmtKeysToRemove as $stmtKeyToRemove) {
                unset($stmts[$stmtKeyToRemove]);
            }

            // replace first extension argument
            $expressoin = $stmts[$firstStmtKey];
            if (! $expressoin instanceof Expression) {
                continue;
            }

            $methodCall = $expressoin->expr;
            if (! $methodCall instanceof MethodCall) {
                continue;
            }

            $methodCall->args[1]->value = new Array_($newArrayItems);
        }

        return $stmts;
    }

    /**
     * @param Expr[][] $extensionExprs
     * @return array<ArrayItem|null>
     */
    private function resolveMergedArrayItems(array $extensionExprs): array
    {
        $newArrayItems = [];
        foreach ($extensionExprs as $stmtKeyToArray) {
            foreach ($stmtKeyToArray as $array) {
                if (! $array instanceof Array_) {
                    continue;
                }

                $newArrayItems = array_merge($newArrayItems, $array->items);
            }
        }

        return $newArrayItems;
    }

    /**
     * @param Expr[][] $extensionStmts
     */
    private function resolveFirstStmtKey(array $extensionStmts): int
    {
        foreach ($extensionStmts as $stmtKeyToArray) {
            return (int) array_key_first($stmtKeyToArray);
        }

        throw new ShouldNotHappenException();
    }

    /**
     * @param Expr[][] $extensionStmts
     * @return int[]
     */
    private function resolveStmtKeysToRemove(array $extensionStmts): array
    {
        $stmtKeysToRemove = [];

        $firstKey = null;
        foreach ($extensionStmts as $stmtKeyToArray) {
            foreach (array_keys($stmtKeyToArray) as $stmtKey) {
                /** @var int $stmtKey */
                if ($firstKey === null) {
                    $firstKey = $stmtKey;
                } else {
                    $stmtKeysToRemove[] = $stmtKey;
                }
            }
        }

        return $stmtKeysToRemove;
    }
}
