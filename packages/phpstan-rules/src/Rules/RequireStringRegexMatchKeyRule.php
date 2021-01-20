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
use PhpParser\Node\Scalar\LNumber;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        NodeFinder $nodeFinder,
        NodeComparator $nodeComparator,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
        $this->simpleNameResolver = $simpleNameResolver;
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
        if (! $parent instanceof Node) {
            return [];
        }

        // assignment can be inside If_, While_, Do_, we need to locate its stmts
        /** @var Node[]|Node|null $locate */
        $locate = property_exists($parent, 'stmts')
            ? $parent->stmts
            : $parent;

        $nextUsedAsArrayDimFetch = $this->getNextUsedAsArrayDimFetch($locate, $node->var);
        if (! $nextUsedAsArrayDimFetch instanceof ArrayDimFetch) {
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

    /**
     * @param Node[]|Node|null $nodes
     */
    private function getNextUsedAsArrayDimFetch($nodes, Expr $expr): ?ArrayDimFetch
    {
        if (is_array($nodes)) {
            return $this->getArrayDimFetchInStmts($nodes, $expr);
        }

        if (! $nodes instanceof Node) {
            return null;
        }

        $next = $nodes->getAttribute(PHPStanAttributeKey::NEXT);

        if (! $next instanceof Node) {
            return null;
        }

        $arrayDimFetch = $this->nodeFinder->findFirst($next, function (Node $n) use ($expr): bool {
            if (! $n instanceof ArrayDimFetch) {
                return false;
            }
            if (! $n->dim instanceof LNumber) {
                return false;
            }
            return $this->nodeComparator->areNodesEqual($n->var, $expr);
        });

        if ($arrayDimFetch instanceof ArrayDimFetch) {
            return $arrayDimFetch;
        }

        return $this->getNextUsedAsArrayDimFetch($next, $expr);
    }

    private function getArrayDimFetchInStmts(array $array, Expr $expr): ?ArrayDimFetch
    {
        foreach ($array as $node) {
            $arrayDimFetch = $this->getNextUsedAsArrayDimFetch($node, $expr);
            if ($arrayDimFetch !== null) {
                return $arrayDimFetch;
            }
        }

        return null;
    }

    private function shouldSkipExpr(Expr $expr): bool
    {
        if (! $expr instanceof StaticCall) {
            return true;
        }

        if (! $this->simpleNameResolver->isName($expr->class, Strings::class)) {
            return true;
        }

        return ! $this->simpleNameResolver->isName($expr->name, 'match');
    }
}
