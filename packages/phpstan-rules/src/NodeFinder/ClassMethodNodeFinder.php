<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

final class ClassMethodNodeFinder
{
    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNodeFinder $simpleNodeFinder, SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNodeFinder = $simpleNodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function findByMethodCall(MethodCall $methodCall): ?ClassMethod
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }

        /** @var string|null $methodCallName */
        $methodCallName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodCallName === null) {
            return null;
        }

        /** @var ClassMethod|null $classMethod */
        $classMethod = $class->getMethod($methodCallName);
        if (! $classMethod instanceof ClassMethod) {
            return null;
        }

        if (! $classMethod->isPrivate()) {
            return null;
        }

        return $classMethod;
    }
}
