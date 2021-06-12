<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\StaticDetector\Collector\StaticNodeCollector;
use Symplify\StaticDetector\Strings\StringsFilter;
use Symplify\StaticDetector\ValueObject\Option;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class StaticCollectNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const ALLOWED_METHOD_NAMES = ['getSubscribedEvents'];

    private ?ClassLike $currentClassLike = null;

    public function __construct(
        private StaticNodeCollector $staticNodeCollector,
        private ParameterProvider $parameterProvider,
        private StringsFilter $stringsFilter,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function enterNode(Node $node)
    {
        $this->ensureClassLikeOrStaticCall($node);

        if ($node instanceof ClassMethod) {
            $this->enterClassMethod($node);
        }

        return null;
    }

    private function ensureClassLikeOrStaticCall(Node $node): void
    {
        if ($node instanceof ClassLike) {
            $this->currentClassLike = $node;
        }

        if ($node instanceof StaticCall) {
            if ($this->currentClassLike !== null) {
                $this->staticNodeCollector->addStaticCallInsideClass($node, $this->currentClassLike);
            } else {
                $this->staticNodeCollector->addStaticCall($node);
            }
        }
    }

    private function enterClassMethod(ClassMethod $classMethod): void
    {
        if (! $classMethod->isStatic()) {
            return;
        }

        $classMethodName = (string) $classMethod->name;
        if (in_array($classMethodName, self::ALLOWED_METHOD_NAMES, true)) {
            return;
        }

        if ($this->currentClassLike === null) {
            $errorMessage = sprintf('Class not found for static call "%s"', $classMethodName);
            throw new ShouldNotHappenException($errorMessage);
        }

        $currentClassName = $this->simpleNameResolver->getName($this->currentClassLike);
        if ($currentClassName === null) {
            return;
        }

        // is filter match?
        $filterClasses = (array) $this->parameterProvider->provideParameter(Option::FILTER_CLASSES);
        if (! $this->stringsFilter->isMatchOrFnMatch($currentClassName, $filterClasses)) {
            return;
        }

        $this->staticNodeCollector->addStaticClassMethod($classMethod, $this->currentClassLike);
    }
}
