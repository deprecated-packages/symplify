<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\PhpParser\NodeFactory;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class ConfiguratorClosureNodeFactory
{
    /**
     * @param Node[] $stmts
     */
    public function createContainerClosureFromStmts(array $stmts): Closure
    {
        $param = $this->createContainerConfiguratorParam();
        return $this->createClosureFromParamAndStmts($param, $stmts);
    }

    /**
     * @param Node[] $stmts
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

    private function createClosureFromParamAndStmts(Param $param, array $stmts): Closure
    {
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
}
