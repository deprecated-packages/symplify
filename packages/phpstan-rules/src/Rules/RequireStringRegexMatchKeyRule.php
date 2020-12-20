<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
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
        return [ArrayDimFetch::class];
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $dim = $node->dim;
        if (! $dim instanceof LNumber) {
            return [];
        }

        $regexMatchAssign = $this->getRegexMatchAssign($node);
        if ($regexMatchAssign === null) {
            return [];
        }

        /** @var StaticCall $expr */
        $expr = $regexMatchAssign->expr;
        /** @var ClassConstFetch $value */
        $value = $expr->args[1]->value;
        $regex = (string) $value->getAttribute(PHPStanAttributeKey::PHPSTAN_CACHE_PRINTER);

        return [sprintf(self::ERROR_MESSAGE, $regex)];
    }

    public function getRegexMatchAssign(ArrayDimFetch $arrayDimFetch): ?Assign
    {
        $parent = $arrayDimFetch->getAttribute(PHPStanAttributeKey::PARENT);
        while ($parent) {
            /** @var Assign[] $assigns */
            $assigns = $this->nodeFinder->find($parent, function (Node $node) use ($arrayDimFetch): bool {
                if (! $node instanceof Assign) {
                    return false;
                }

                return $this->nodeComparator->areNodesEqual($node->var, $arrayDimFetch->var);
            });

            if ($assigns === []) {
                $parent = $parent->getAttribute(PHPStanAttributeKey::PARENT);
                continue;
            }

            // ensure use last assign to ensure use latest assign
            $lastAssign = $assigns[count($assigns) - 1];
            if ($this->isExprStringsMatch($lastAssign)) {
                return $lastAssign;
            }

            $parent = $parent->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
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

    private function isExprStringsMatch(?Assign $assign): bool
    {
        if (! $assign instanceof Assign) {
            return false;
        }

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

        return $assign->expr->name->toString() === 'match';
    }
}
