<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
        Do_::class => ['cond'],
        For_::class => ['init', 'cond', 'loop'],
        Foreach_::class => ['expr', 'keyVar', 'valueVar'],
        While_::class => ['cond'],
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(NodeFinder $nodeFinder, SimpleNameResolver $simpleNameResolver)
    {
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
        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($node->stmts, Assign::class);
        if ($assigns === []) {
            return [];
        }

        $nodeClass = get_class($node);
        foreach (self::LOOP_STMTS_CHECKS[$nodeClass] as $expr) {
            /** @var Variable[] $variables */
            $variables = $this->nodeFinder->find($node->{$expr}, function (Node $n): bool {
                return $n instanceof Variable;
            });

            foreach ($assigns as $assign) {
                if ($this->isInAssign($variables, $assign)) {
                    return [];
                }

                /** @var Variable[] $variables */
                $variablesInAssign = $this->nodeFinder->find($assign, function (Node $n): bool {
                    return $n instanceof Variable;
                });

                if ($this->isUsedInPreviously($variablesInAssign, $node)) {
                    return [];
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

    /**
     * @param Variable[] $variables
     */
    private function isInAssign(array $variables, Assign $assign): bool
    {
        foreach ($variables as $variable) {
            $isInAssign = (bool) $this->nodeFinder->findFirst($assign, function (Node $n) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($n, $variable);
            });
            if ($isInAssign) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Variable[] $variables
     * @param Do_|For_|Foreach_|While_ $node
     */
    private function isUsedInPreviously(array $variables, Node $node): bool
    {
        $previous = $node->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if (! $previous instanceof Node) {
            $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
            if (! $parent instanceof Node) {
                return false;
            }

            return $this->isUsedInPreviously($variables, $parent);
        }

        foreach ($variables as $variable) {
            $isInPrevious = (bool) $this->nodeFinder->findFirst($previous, function (Node $n) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($n, $variable);
            });
            if ($isInPrevious) {
                return true;
            }
        }

        return false;
    }
}
