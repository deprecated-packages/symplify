<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipExpr($node->expr)) {
            return [];
        }

        /** @var Node|null $parent */
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        $nextUsedAsArrayDimFetch = $this->getNextUsedAsArrayDimFetch($parent, $node->var);

        if ($nextUsedAsArrayDimFetch === null) {
            return [];
        }

        $dim = $nextUsedAsArrayDimFetch->dim;
        if (! $dim instanceof LNumber) {
            return [];
        }

        /** @var StaticCall $expr */
        $expr = $node->expr;
        /** @var ClassConstFetch $value */
        $value = $expr->args[1]->value;
        $regex = (string) $value->getAttribute(PHPStanAttributeKey::PHPSTAN_CACHE_PRINTER);

        return [sprintf(self::ERROR_MESSAGE, $regex)];
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

    private function getNextUsedAsArrayDimFetch(?Node $node, Expr $expr): ?ArrayDimFetch
    {
        if (! $node instanceof Node) {
            return null;
        }

        // assignment can be inside If_, While_, Do_, we need to locate its stmts etc
        /** @var Node[]|Node $next */
        $next = property_exists($node, 'stmts')
            ? $node->stmts
            : $node->getAttribute(PHPStanAttributeKey::NEXT);

        if ($next === null) {
            return null;
        }

        $arrayDimFetch = $this->nodeFinder->findFirst($next, function (Node $n) use ($expr): bool {
            return $n instanceof ArrayDimFetch && $this->nodeComparator->areNodesEqual($n->var, $expr);
        });

        if ($arrayDimFetch instanceof ArrayDimFetch) {
            return $arrayDimFetch;
        }

        return $this->getNextUsedAsArrayDimFetch($next, $expr);
    }

    private function shouldSkipExpr(Expr $expr): bool
    {
        if (! $expr instanceof StaticCall) {
            return true;
        }

        if (! $expr->class instanceof FullyQualified) {
            return true;
        }

        if ($expr->class->toString() !== Strings::class) {
            return true;
        }

        if (! $expr->name instanceof Identifier) {
            return true;
        }

        return $expr->name->toString() !== 'match';
    }
}
