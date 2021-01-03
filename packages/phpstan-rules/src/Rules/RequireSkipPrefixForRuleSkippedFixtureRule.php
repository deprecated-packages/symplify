<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PHPStan\Analyser\Scope;
use PHPStan\Testing\RuleTestCase;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule\RequireSkipPrefixForRuleSkippedFixtureRuleTest
 */
final class RequireSkipPrefixForRuleSkippedFixtureRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'File "%s" should have prefix "skip"';

    /**
     * @var NodeValueResolver
     */
    private $nodeValueResolver;

    public function __construct(NodeValueResolver $nodeValueResolver)
    {
        $this->nodeValueResolver = $nodeValueResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Array_::class];
    }

    /**
     * @param Array_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipClassName($scope)) {
            return [];
        }

        if (count($node->items) !== 2) {
            return [];
        }

        $firstItem = $node->items[0];
        if (! $firstItem instanceof ArrayItem) {
            return [];
        }

        $secondItem = $node->items[1];
        if (! $secondItem instanceof ArrayItem) {
            return [];
        }

        if (! $secondItem->value instanceof Array_) {
            return [];
        }

        if (count($secondItem->value->items) !== 0) {
            return [];
        }

        if (! $firstItem->value instanceof Concat) {
            return [];
        }

        $filePath = $this->nodeValueResolver->resolve($firstItem->value->right);
        $fileBaseName = (string) Strings::after($filePath, '/', -1);
        if (Strings::startsWith($fileBaseName, 'Skip')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $fileBaseName)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPStan\Testing\RuleTestCase;

final class SomeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NewWithInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return new SomeRule());
    }
}

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPStan\Testing\RuleTestCase;

final class SomeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNewWithInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return new SomeRule());
    }
}

CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipClassName(Scope $scope): bool
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return true;
        }

        return ! is_a($className, RuleTestCase::class, true);
    }
}
