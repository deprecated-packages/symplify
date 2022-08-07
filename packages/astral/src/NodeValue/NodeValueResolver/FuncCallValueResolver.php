<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<FuncCall>
 */
final class FuncCallValueResolver implements NodeValueResolverInterface
{
    /**
     * @var string[]
     */
    private const EXCLUDED_FUNC_NAMES = ['pg_*'];

    public function __construct(
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
    public function resolve(Expr $expr, string $currentFilePath): mixed
    {
        if ($expr->name instanceof Name && $expr->name->toString() === 'getcwd') {
            return dirname($currentFilePath);
        }

        $args = $expr->getArgs();
        $arguments = [];
        foreach ($args as $arg) {
            $arguments[] = $this->constExprEvaluator->evaluateDirectly($arg->value);
        }

        if ($expr->name instanceof Name) {
            $functionName = (string) $expr->name;

            if (! $this->isAllowedFunctionName($functionName)) {
                return null;
            }

            if (function_exists($functionName)) {
                return $functionName(...$arguments);
            }

            throw new ShouldNotHappenException();
        }

        return null;
    }

    private function isAllowedFunctionName(string $functionName): bool
    {
        foreach (self::EXCLUDED_FUNC_NAMES as $excludedFuncName) {
            if (fnmatch($excludedFuncName, $functionName, FNM_NOESCAPE)) {
                return false;
            }
        }

        return true;
    }
}
