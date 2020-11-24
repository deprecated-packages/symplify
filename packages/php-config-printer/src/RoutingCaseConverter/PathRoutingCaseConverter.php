<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\RoutingCaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class PathRoutingCaseConverter implements RoutingCaseConverterInterface
{
    /**
     * @var string[]
     */
    private const NESTED_KEYS = ['controller', 'defaults', self::METHODS, 'requirements'];

    /**
     * @var string
     */
    private const PATH = 'path';

    /**
     * @var string
     */
    private const METHODS = 'methods';

    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;

    public function __construct(ArgsNodeFactory $argsNodeFactory)
    {
        $this->argsNodeFactory = $argsNodeFactory;
    }

    public function match(string $key, $values): bool
    {
        return isset($values[self::PATH]);
    }

    public function convertToMethodCall(string $key, $values): Expression
    {
        $variable = new Variable(VariableName::ROUTING_CONFIGURATOR);

        // @todo args

        $args = $this->createAddArgs($key, $values);
        $methodCall = new MethodCall($variable, 'add', $args);

        foreach (self::NESTED_KEYS as $nestedKey) {
            if (! isset($values[$nestedKey])) {
                continue;
            }

            $nestedValues = $values[$nestedKey];

            // Transform methods as string GET|HEAD to array
            if ($nestedKey === self::METHODS && is_string($nestedValues)) {
                $nestedValues = explode('|', $nestedValues);
            }

            $args = $this->argsNodeFactory->createFromValues([$nestedValues]);
            $methodCall = new MethodCall($methodCall, $nestedKey, $args);
        }

        return new Expression($methodCall);
    }

    /**
     * @param mixed $values
     * @return Arg[]
     */
    private function createAddArgs(string $key, $values): array
    {
        $argumentValues = [];
        $argumentValues[] = $key;

        if (isset($values[self::PATH])) {
            $argumentValues[] = $values[self::PATH];
        }

        return $this->argsNodeFactory->createFromValues($argumentValues);
    }
}
