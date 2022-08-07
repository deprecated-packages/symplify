<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedCallInAssertMethodCallRule\ForbiddenNestedCallInAssertMethodCallRuleTest
 */
final class ForbiddenNestedCallInAssertMethodCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Decouple method call in assert to standalone line to make test core more readable';

    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if ($this->shouldSkipMethodName($methodName, $node)) {
            return [];
        }

        $argMethodCall = $this->nodeFinder->findFirstInstanceOf($node->getArgs()[1], MethodCall::class);
        if (! $argMethodCall instanceof MethodCall) {
            return [];
        }

        if ($argMethodCall->getArgs() === []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SomeClass extends TestCase
{
    public function test()
    {
        $this->assertSame('oooo', $this->someMethodCall());
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SomeClass extends TestCase
{
    public function test()
    {
        $result = $this->someMethodCall();
        $this->assertSame('oooo', $result);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipMethodName(string $methodName, MethodCall $methodCall): bool
    {
        if (! \str_starts_with($methodName, 'assert')) {
            return true;
        }

        if (in_array($methodName, ['assertTrue', 'assertFalse'], true)) {
            return true;
        }

        return count($methodCall->args) <= 1;
    }
}
