<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<FuncCall>
 */
final class FuncCallValueResolver implements NodeValueResolverInterface
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ConstExprEvaluator $constExprEvaluator
    ) {
    }

    public function getType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $expr
     */
    public function resolve(Expr $expr, string $currentFilePath): ?string
    {
        if ($expr instanceof FuncCall && $this->simpleNameResolver->isName($expr, 'getcwd')) {
            return dirname($currentFilePath);
        }

        $args = $expr->args;
        $arguments = [];
        foreach ($args as $arg) {
            try {
                $argValue = $this->constExprEvaluator->evaluateDirectly($arg->value);
            } catch (ConstExprEvaluationException) {
                $argValue = null;
            }
            $arguments[] = $argValue;
        }

        if ($expr->name instanceof Name) {
            $functionName = (string) $expr->name;
            if (! is_callable($functionName)) {
                throw new ShouldNotHappenException();
            }
            return call_user_func_array($functionName, $arguments);
        }
        return null;
    }
}
