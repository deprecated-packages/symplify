<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule\CheckTraitMethodOnlyDelegateOtherClassRuleTest
 */
final class CheckTraitMethodOnlyDelegateOtherClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait method %s should only delegate other class';

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
        return [Trait_::class];
    }

    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /* @var ClassMethod[] $classMethods */
        $classMethods = $this->nodeFinder->findInstanceOf($node, ClassMethod::class);

        foreach ($classMethods as $classMethod) {
            /* @var MethodCall[] $methodCalls */
            $methodCalls = $this->nodeFinder->findInstanceOf($node, MethodCall::class);

            foreach ($methodCalls as $methodCall) {
                // ...
            }
        }

        return [];
    }
}
