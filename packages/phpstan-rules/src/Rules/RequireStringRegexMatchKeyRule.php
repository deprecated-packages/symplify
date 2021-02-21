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
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\Astral\ValueObject\NodeFinder\ScopeTypes;
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
    public const ERROR_MESSAGE = 'Regex must use string named capture groups instead of numeric';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    public function __construct(
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver,
        ParentNodeFinder $parentNodeFinder
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
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

        if (! $node->var instanceof Variable) {
            return [];
        }

        $scopeNode = $this->parentNodeFinder->findFirstParentByTypes($node, ScopeTypes::STMT_TYPES);
        if ($scopeNode === null) {
            return [];
        }

        $usedAsArrayDimFetches = $this->findVariableArrayDimFetches($scopeNode, $node->var);
        if ($usedAsArrayDimFetches === []) {
            return [];
        }

        /** @var StaticCall $expr */
        $expr = $node->expr;

        /** @var ClassConstFetch $value */
        $value = $expr->args[1]->value;

        return [self::ERROR_MESSAGE];
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
    private const REGEX = '#(?<content>a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches['content'];
        }
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return ArrayDimFetch[]
     */
    private function findVariableArrayDimFetches(Node $node, Variable $variable): array
    {
        $variableName = $this->simpleNameResolver->getName($variable);
        if ($variableName === null) {
            return [];
        }

        return $this->nodeFinder->find($node, function (Node $node) use ($variableName): bool {
            if (! $node instanceof ArrayDimFetch) {
                return false;
            }

            if (! $node->var instanceof Variable) {
                return false;
            }

            if (! $node->dim instanceof LNumber) {
                return false;
            }

            return $this->simpleNameResolver->isName($node->var, $variableName);
        });
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
