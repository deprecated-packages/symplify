<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule\ForbiddenInlineClassMethodRuleTest
 */
final class ForbiddenInlineClassMethodRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" only calling another method call and has no added value. Use the inlined call instead';

    public function __construct(
        private NodeFinder $nodeFinder,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
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

            $methodName = $classMethod->name->toString();

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
        $methodCalls = $this->nodeFinder->findInstanceOf($class, MethodCall::class);

        $usedMethodCalls = [];
        foreach ($methodCalls as $methodCall) {
            if (! $methodCall->name instanceof Identifier) {
                continue;
            }

            $methodCallName = $methodCall->name->toString();
            if ($methodCallName !== $methodName) {
                continue;
            }

            // is local variable?
            if (! $methodCall->var instanceof Variable) {
                continue;
            }

            if (! is_string($methodCall->var->name)) {
                continue;
            }

            if ($methodCall->var->name !== 'this') {
                continue;
            }

            $usedMethodCalls[] = $methodCall;
        }

        return $usedMethodCalls;
    }
}
