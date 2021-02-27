<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Clone_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Symplify\PHPStanRules\NodeFinder\ReturnNodeFinder;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\NoModifyAndReturnSelfObjectRuleTest
 */
final class NoModifyAndReturnSelfObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use void instead of modify and return self object';

    /**
     * @var ReturnNodeFinder
     */
    private $returnNodeFinder;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    public function __construct(ReturnNodeFinder $returnNodeFinder, NodeFinder $nodeFinder, NodeComparator $nodeComparator)
    {
        $this->returnNodeFinder = $returnNodeFinder;
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
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
        if ($node->params === []) {
            return [];
        }

        $returns = $this->returnNodeFinder->findReturnsWithValues($node);

        if ($returns === []) {
            return [];
        }

        foreach ($returns as $return) {
            if (! $return->expr instanceof Expr) {
                continue;
            }

            if (! $this->isClone($return, $return->expr)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    private function isClone(Node $return, Expr $expr): bool
    {
        $filter = function (Node $node) use ($expr): bool {
            if (! $node instanceof Assign) {
                return false;
            }

            if (! $node->expr instanceof Clone_) {
                return false;
            }

            return $this->nodeComparator->areNodesEqual($node->var, $expr);
        };

        $previousStatement = $return->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if ($previousStatement !== null) {
            $foundNode = $this->nodeFinder->findFirst([$previousStatement], $filter);
            if ($foundNode !== null) {
                return true;
            }

            return $this->isClone($previousStatement, $expr);
        }

        $parent = $return->getAttribute(PHPStanAttributeKey::PARENT);
        if ($parent instanceof Node) {
            return $this->isClone($parent, $expr);
        }

        return false;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function modify(ComposerJson $composerJson): ComposerJson
    {
        $composerJson->addRequiredPackage($this->packageName, $this->version->getVersion());
        return $composerJson;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function modify(ComposerJson $composerJson): void
    {
        $composerJson->addRequiredPackage($this->packageName, $this->version->getVersion());
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
