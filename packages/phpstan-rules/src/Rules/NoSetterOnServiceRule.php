<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoSetterOnServiceRule\NoSetterOnServiceRuleTest
 */
final class NoSetterOnServiceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use setter on a service';

    /**
     * @var string
     * @see https://regex101.com/r/yI3qGS/2
     */
    private const NOT_A_SERVICE_NAMESPACE_REGEX = '#\bEntity|Event|ValueObject\b#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $fullyQualifiedClassName = $this->getClassName($scope);
        if ($fullyQualifiedClassName === null) {
            return [];
        }

        if (Strings::match($fullyQualifiedClassName, self::NOT_A_SERVICE_NAMESPACE_REGEX)) {
            return [];
        }

        if (! $node->isPublic()) {
            return [];
        }

        $classMethodName = $node->name->toString();
        if (! Strings::startsWith($classMethodName, 'set')) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf((array) $node->getStmts(), Assign::class);
        foreach ($assigns as $assign) {
            $assignVariable = $assign->var;
            if ($assignVariable instanceof PropertyFetch || $assignVariable instanceof StaticPropertyFetch) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
