<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedCallInAssertMethodCallRule\ForbiddenNestedCallInAssertMethodCallRuleTest
 */
final class ForbiddenNestedCallInAssertMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Decouple method call in assert to standalone line to make test core more readable';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(SimpleNameResolver $simpleNameResolver, NodeFinder $nodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methodName = $this->simpleNameResolver->getName($node->name);
        if ($methodName === null) {
            return [];
        }

        if ($this->shouldSkipMethodName($methodName, $node)) {
            return [];
        }

        $argMethodCall = $this->nodeFinder->findFirstInstanceOf($node->args[1], MethodCall::class);
        if (! $argMethodCall instanceof \PhpParser\Node\Expr\MethodCall) {
            return [];
        }

        if ($argMethodCall->args === []) {
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
        $this->assetSame('oooo', $this->someMethodCall());
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
        $this->assetSame('oooo', $result);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipMethodName(string $methodName, MethodCall $methodCall): bool
    {
        if (! Strings::startsWith($methodName, 'assert')) {
            return true;
        }

        if (in_array($methodName, ['assertTrue', 'assertFalse'], true)) {
            return true;
        }

        return count($methodCall->args) <= 1;
    }
}
