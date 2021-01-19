<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\ForbiddenAssignInLoopRuleTest
 */
final class ForbiddenAssignInLoopRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign in loop is not allowed.';

    /**
     * @var array<string, array<int, string>>
     */
    private const LOOP_STMTS_CHECKS = [
        Do_::class => [
            'cond'
        ],
        For_::class => [
            'init',
            'cond',
            'loop',
        ],
        Foreach_::class => [
            'expr',
            'keyVar',
            'valueVar',
        ],
        While_::class => [
            'cond',
        ],
    ];

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var Expr
     */
    private $assignVariable;

    public function __construct(ParentNodeFinder $parentNodeFinder, NodeFinder $nodeFinder, SimpleNameResolver $simpleNameResolver)
    {
        $this->parentNodeFinder = $parentNodeFinder;
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Do_::class, For_::class, Foreach_::class, While_::class];
    }

    /**
     * @param Do_|For_|Foreach_|While_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $assigns = $this->nodeFinder->findInstanceOf($node, Assign::class);
        if ($assigns === []) {
            return [];
        }

        $nodeClass = get_class($node);
        foreach (self::LOOP_STMTS_CHECKS[$nodeClass] as $expr) {
            $variables = $this->nodeFinder->find($node->$expr, function (Node $n) : bool {
                return $n instanceof Variable;
            });

            if ($variables === []) {
                continue;
            }

            foreach ($assigns as $assign) {
                foreach ($variables as $variable) {
                    $isInAssign = (bool) $this->nodeFinder->findFirst($assign, function (Node $n) use ($variable): bool {
                        return $this->simpleNameResolver->areNamesEqual($n, $variable);
                    });
                    if ($isInAssign) {
                        return [];
                    }
                }
            }
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
foreach (...) {
    $value = new SmartFileInfo('a.php');
    if ($value) {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = new SmartFileInfo('a.php');
foreach (...) {
    if ($value) {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
