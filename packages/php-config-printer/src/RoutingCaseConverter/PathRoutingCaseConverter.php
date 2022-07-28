<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\RoutingCaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\PhpConfigPrinter\Enum\RouteOption;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class PathRoutingCaseConverter implements RoutingCaseConverterInterface
{
    public function __construct(
        private ArgsNodeFactory $argsNodeFactory
    ) {
    }

    public function match(string $key, mixed $values): bool
    {
        return isset($values[RouteOption::PATH]);
    }

    public function convertToMethodCall(string $key, mixed $values): Expression
    {
        $variable = new Variable(VariableName::ROUTING_CONFIGURATOR);

        // @todo args

        $args = $this->createAddArgs($key, $values);
        $methodCall = new MethodCall($variable, 'add', $args);

        foreach (RouteOption::ALL as $nestedKey) {
            if (! isset($values[$nestedKey])) {
                continue;
            }

            $nestedValues = $values[$nestedKey];

            // Transform methods as string GET|HEAD to array
            if ($nestedKey === RouteOption::METHODS && is_string($nestedValues)) {
                $nestedValues = explode('|', $nestedValues);
            }

            $args = $this->argsNodeFactory->createFromValues([$nestedValues]);
            $methodCall = new MethodCall($methodCall, $nestedKey, $args);
        }

        return new Expression($methodCall);
    }

    /**
     * @return Arg[]
     */
    private function createAddArgs(string $key, mixed $values): array
    {
        $argumentValues = [];
        $argumentValues[] = $key;

        if (isset($values[RouteOption::PATH])) {
            $argumentValues[] = $values[RouteOption::PATH];
        }

        return $this->argsNodeFactory->createFromValues($argumentValues);
    }
}
