<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoRightPHPUnitAssertScalarRule\NoRightPHPUnitAssertScalarRuleTest
 */
final class NoRightPHPUnitAssertScalarRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The compare assert arguments are switched. Move the expected value to the 1st left';

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
        if (! in_array($methodName, ['assertSame', 'assertEquals'], true)) {
            return [];
        }

        // is 2nd argument a scalar? should not be
        $secondArg = $node->getArgs()[1];

        $secondArgValue = $secondArg->value;
        if (! $this->isScalarValue($secondArgValue)) {
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

final class SomeFlippedAssert extends TestCase
{
    public function test()
    {
        $value = 1000;
        $this->assertSame($value, 10);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SomeFlippedAssert extends TestCase
{
    public function test()
    {
        $value = 1000;
        $this->assertSame(10, $value);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isScalarValue(Expr $expr): bool
    {
        if ($expr instanceof Scalar) {
            return true;
        }

        if ($expr instanceof ConstFetch) {
            return true;
        }

        return $expr instanceof ClassConstFetch;
    }
}
