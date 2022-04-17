<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\While_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\NoPropertySetOverrideRuleTest
 */
final class NoPropertySetOverrideRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property set "%s" is overridden.';

    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private Standard $printerStandard
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @param InClassMethodNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classMethod = $node->getOriginalNode();

        /** @var Assign[] $assigns */
        $assigns = $this->simpleNodeFinder->findByType($classMethod, Assign::class);

        $propertyFetchesByContent = $this->groupPropertyFetchesByContent($assigns);

        $ruleErrors = [];
        foreach ($propertyFetchesByContent as $propertyFetches) {
            if (\count($propertyFetches) < 2) {
                continue;
            }

            /** @var PropertyFetch $lastPropertyFetch */
            $lastPropertyFetch = \array_pop($propertyFetches);
            $ruleErrors[] = $this->createRuleError($lastPropertyFetch);
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$someObject = new SomeClass();
$someObject->name = 'First value';

// ...
$someObject->name = 'Second value';
CODE_SAMPLE
            ,
                <<<'CODE_SAMPLE'
$someObject = new SomeClass();
$someObject->name = 'First value';
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Assign[] $assigns
     * @return array<string, PropertyFetch[]>
     */
    private function groupPropertyFetchesByContent(array $assigns): array
    {
        $propertyFetchesByContent = [];

        foreach ($assigns as $assign) {
            $assignedVar = $assign->var;
            if (! $assignedVar instanceof PropertyFetch) {
                continue;
            }

            $cacheKey = $this->createCacheKey($assignedVar);
            $propertyFetchesByContent[$cacheKey][] = $assignedVar;
        }

        return $propertyFetchesByContent;
    }

    private function createCacheKey(PropertyFetch $propertyFetch): string
    {
        $parentScopeNode = $this->simpleNodeFinder->findFirstParentByTypes($propertyFetch, [
            If_::class, Else_::class, ElseIf_::class, While_::class, For_::class, Foreach_::class, Switch_::class,
            Case_::class, Match_::class, Closure::class,
        ]);

        $cacheKey = $this->printerStandard->prettyPrintExpr($propertyFetch);

        if (! $parentScopeNode instanceof Node) {
            return $cacheKey;
        }

        return $cacheKey . '_' . $parentScopeNode::class . '_ ' . \spl_object_hash($parentScopeNode);
    }

    private function createRuleError(PropertyFetch $propertyFetch): RuleError
    {
        $propertyFetchContent = $this->printerStandard->prettyPrintExpr($propertyFetch);
        $errorMessage = \sprintf(self::ERROR_MESSAGE, $propertyFetchContent);

        return RuleErrorBuilder::message($errorMessage)
            ->line($propertyFetch->getLine())
            ->build();
    }
}
