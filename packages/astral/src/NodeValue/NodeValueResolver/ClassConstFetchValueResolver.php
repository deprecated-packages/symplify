<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\ClassLike;
use ReflectionClassConstant;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<ClassConstFetch>
 */
final class ClassConstFetchValueResolver implements NodeValueResolverInterface
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder,
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
            $classLike = $this->simpleNodeFinder->findFirstParentByType($expr, ClassLike::class);
            if (! $classLike instanceof ClassLike) {
                return null;
            }

            $className = $this->simpleNameResolver->getName($classLike);
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
