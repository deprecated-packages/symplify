<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
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

    /**
     * @var StaticNodeCollector
     */
    private $staticNodeCollector;

    /**
     * @var ClassLike|null
     */
    private $currentClassLike;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var StringsFilter
     */
    private $stringsFilter;

    public function __construct(
        StaticNodeCollector $staticNodeCollector,
        ParameterProvider $parameterProvider,
        StringsFilter $stringsFilter
    ) {
        $this->staticNodeCollector = $staticNodeCollector;
        $this->parameterProvider = $parameterProvider;
        $this->stringsFilter = $stringsFilter;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof ClassLike) {
            $this->currentClassLike = $node;
        }

        if ($node instanceof StaticCall) {
            $this->staticNodeCollector->addStaticCall($node, $this->currentClassLike);
        }

        if ($node instanceof ClassMethod) {
            if (! $node->isStatic()) {
                return null;
            }

            $classMethodName = (string) $node->name;
            if (in_array($classMethodName, self::ALLOWED_METHOD_NAMES, true)) {
                return null;
            }

            if ($this->currentClassLike === null) {
                throw new ShouldNotHappenException('Class not found for static call');
            }

            // is filter match?
            $filterClasses = (array) $this->parameterProvider->provideParameter(Option::FILTER_CLASSES);
            $currentClassName = (string) $this->currentClassLike->namespacedName;
            if (! $this->stringsFilter->isMatchOrFnMatch($currentClassName, $filterClasses)) {
                return null;
            }

            $this->staticNodeCollector->addStaticClassMethod($node, $this->currentClassLike);
        }

        return null;
    }
}
