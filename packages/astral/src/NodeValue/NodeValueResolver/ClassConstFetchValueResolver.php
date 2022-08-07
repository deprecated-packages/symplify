<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\ConstExprEvaluationException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use ReflectionClassConstant;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<ClassConstFetch>
 */
final class ClassConstFetchValueResolver implements NodeValueResolverInterface
{
    public function getType(): string
    {
        return ClassConstFetch::class;
    }

    /**
     * @param ClassConstFetch $expr
     */
    public function resolve(Expr $expr, string $currentFilePath): mixed
    {
        if (! $expr->class instanceof Name) {
            return null;
        }

        $className = $expr->class->toString();
        if ($className === 'self') {
            // unable to resolve
            throw new ConstExprEvaluationException('Unable to resolve self class constant');
        }

        if (! $expr->name instanceof Identifier) {
            return null;
        }

        $constantName = $expr->name->toString();
        if ($constantName === 'class') {
            return $className;
        }

        if (! class_exists($className) && ! interface_exists($className)) {
            return null;
        }

        $reflectionClassConstant = new ReflectionClassConstant($className, $constantName);
        return $reflectionClassConstant->getValue();
    }
}
