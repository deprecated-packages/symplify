<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule\ForbiddenInlineClassMethodRuleTest
 */
final class ForbiddenInlineClassMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" only calling another method call and has no added value. Use the inlined call instead';

    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [InClassNode::class];
    }

    /**
     * @param InClassNode $node
     * @return string[]|RuleError[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        foreach ($classLike->getMethods() as $classMethod) {
            if (! $classMethod->isPrivate()) {
                continue;
            }

            // must be exactly one line
            if (count((array) $classMethod->stmts) !== 1) {
                continue;
            }

            $onlyStmt = $classMethod->stmts[0] ?? null;
            if (! $onlyStmt instanceof Return_) {
                continue;
            }

            if (! $onlyStmt->expr instanceof MethodCall) {
                continue;
            }

            /** @var string $methodName */
            $methodName = $this->simpleNameResolver->getName($classMethod->name);

            // this method is called just once in the rest of project
            $usedMethodCalls = $this->findMethodCalls($classLike, $methodName);

            // we look exactly for one match
            if (count($usedMethodCalls) === 1) {
                $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);

                $ruleErrorBuilder = RuleErrorBuilder::message($errorMessage)
                    ->line($classMethod->getLine());

                return [$ruleErrorBuilder->build()];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return $this->away();
    }

    private function away()
    {
        return mt_rand(0, 100);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return mt_rand(0, 100);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return MethodCall[]
     */
    private function findMethodCalls(Class_ $class, string $methodName): array
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->simpleNodeFinder->findByType($class, MethodCall::class);

        $usedMethodCalls = [];
        foreach ($methodCalls as $methodCall) {
            if (! $this->simpleNameResolver->isName($methodCall->name, $methodName)) {
                continue;
            }

            // is local variable?
            if (! $this->simpleNameResolver->isName($methodCall->var, 'this')) {
                continue;
            }

            $usedMethodCalls[] = $methodCall;
        }

        return $usedMethodCalls;
    }
}
