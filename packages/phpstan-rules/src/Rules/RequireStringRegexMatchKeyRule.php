<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\PHPStanRules\Printer\NodeComparator;
use PhpParser\NodeFinder;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use Nette\Utils\Strings;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule\RequireStringRegexMatchKeyRuleTest
 */
final class RequireStringRegexMatchKeyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" regex need to use string named capture group instead of numeric';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    public function __construct(NodeFinder $nodeFinder, NodeComparator $nodeComparator)
    {
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $dim = $node->dim;
        if ($dim instanceof String_) {
            return [];
        }

        if ($this->isNotRegexMatchResult($node)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, '')];
    }

    public function isNotRegexMatchResult(ArrayDimFetch $arrayDimFetch): bool
    {
        $parent = $arrayDimFetch->getAttribute(PHPStanAttributeKey::PARENT);
        while ($parent) {
            $assign = $this->nodeFinder->findFirst($parent, function (Node $node) use ($arrayDimFetch): bool {
                if (! $node instanceof Assign) {
                    return false;
                }

                return $this->nodeComparator->areNodesEqual($node->var, $arrayDimFetch->var);
            });

            if ($assign instanceof Assign) {
                return $this->isNotExprStringsMatch($assign);
            }

            $parent = $parent->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return false;
    }

    private function isNotExprStringsMatch(Assign $assign): bool
    {
        if (! $assign->expr instanceof StaticCall) {
            return false;
        }

        if (! $assign->expr->class instanceof FullyQualified) {
            return false;
        }

        if ($assign->expr->class->toString() !== Strings::class) {
            return false;
        }

        if (! $assign->expr->name instanceof Identifier) {
            return false;
        }

        return $assign->expr->name->toString() !== 'match';
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Utils\Strings;

class SomeClass
{
    private const REGEX = '#(a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches[1];
        }
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Utils\Strings;

class SomeClass
{
    private const REGEX = '#(?<c>a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches['c'];
        }
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
