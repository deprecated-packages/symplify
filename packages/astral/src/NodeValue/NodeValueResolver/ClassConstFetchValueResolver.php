<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\ConstExprEvaluationException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use ReflectionClassConstant;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<ClassConstFetch>
 */
final class ClassConstFetchValueResolver implements NodeValueResolverInterface
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function getType(): string
    {
        return ClassConstFetch::class;
    }

    /**
     * @param ClassConstFetch $expr
     */
    public function resolve(Expr $expr, string $currentFilePath): mixed
    {
        $className = $this->simpleNameResolver->getName($expr->class);

        if ($className === 'self') {
            // unable to resolve
            throw new ConstExprEvaluationException('Unable to resolve self class constant');
        }

        if ($className === null) {
            return null;
        }

        $constantName = $this->simpleNameResolver->getName($expr->name);
        if ($constantName === null) {
            return null;
        }

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
