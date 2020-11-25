<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInIfRule\ForbiddenAssignInIfRuleTest
 */
final class ForbiddenAssignInIfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assignment inside if is not allowed. Extract condition to extra variable on line above';

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
if ($isRandom = mt_rand()) {
    // ...
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$isRandom = mt_rand();
if ($isRandom) {
    // ...
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isHaveAssignmentInside(If_ $if): bool
    {
        return (bool) $this->nodeFinder->findFirst($if->cond, function (Node $node): bool {
            return $node instanceof Assign;
        });
    }
}
