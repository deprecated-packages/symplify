<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule\NoMirrorAssertRuleTest
 */
final class NoMirrorAssertRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The assert is tautology that compares to itself. Fix it to different values';

    public function __construct(
        private Standard $standard,
        private NodeFinder $nodeFinder,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class AssertMirror extends TestCase
{
    public function test()
    {
        $this->assertSame(1, 1);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class AssertMirror extends TestCase
{
    public function test()
    {
        $value = 200;
        $this->assertSame(1, $value);
    }
}
CODE_SAMPLE
            ),
        ]);
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
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isSubclassOf('PHPUnit\Framework\TestCase')) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $errorMessages = [];

        $assertMethodCalls = $this->findAssertMethodCalls($classLike);

        foreach ($assertMethodCalls as $assertMethodCall) {
            if (! $this->areFirstAndSecondArgTheSame($assertMethodCall)) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($assertMethodCall->getLine())
                ->build();
        }

        return $errorMessages;
    }

    private function areFirstAndSecondArgTheSame(MethodCall $methodCall): bool
    {
        $args = $methodCall->getArgs();

        $firstArgValue = $args[0]->value;
        $secondArgValue = $args[1]->value;

        $firstArgValueContent = $this->standard->prettyPrintExpr($firstArgValue);
        $secondArgValueContent = $this->standard->prettyPrintExpr($secondArgValue);

        return $firstArgValueContent === $secondArgValueContent;
    }

    /**
     * @return MethodCall[]
     */
    private function findAssertMethodCalls(Class_ $class): array
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($class->stmts, MethodCall::class);

        $assertMethodCalls = [];

        foreach ($methodCalls as $methodCall) {
            // compare 1st and 2nd value
            if (count($methodCall->getArgs()) < 2) {
                continue;
            }

            // only "assert*" methods
            if (! $methodCall->name instanceof Identifier) {
                continue;
            }

            $methodCallName = $methodCall->name->toString();
            if (! str_starts_with($methodCallName, 'assert')) {
                continue;
            }

            $assertMethodCalls[] = $methodCall;
        }

        return $assertMethodCalls;
    }
}
