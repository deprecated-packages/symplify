<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInifRule\ForbiddenAssignInifRuleTest
 */
final class ForbiddenAssignInifRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assignment inside if is not allowed. Use before if instead.';

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
        return [If_::class];
    }

    /**
     * @param If_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->isHaveAssignmentInside($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isHaveAssignmentInside(If_ $if): bool
    {
        return (bool) $this->nodeFinder->findFirst($if->cond, function (Node $node): bool {
            return $node instanceof Assign;
        });
    }
}
