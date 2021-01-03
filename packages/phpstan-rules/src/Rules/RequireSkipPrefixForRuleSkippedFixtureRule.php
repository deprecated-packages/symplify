<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
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
    public const ERROR_MESSAGE = 'File "%s" should have prefix "Skip" prefix';

    /**
     * @var string
     */
    public const INVERTED_ERROR_MESSAGE = 'File "%s" should not have "Skip" prefix';

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

        if ($this->isEmptyArray($secondItem->value)) {
            return $this->processSkippedFile($firstItem->value);
        }

        return $this->processMatchingFile($firstItem->value);
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

    private function isEmptyArray(Expr $expr): bool
    {
        if (! $expr instanceof Array_) {
            return false;
        }

        return count($expr->items) === 0;
    }

    /**
     * @return string[]
     */
    private function processSkippedFile(Expr $expr): array
    {
        $filePath = $this->nodeValueResolver->resolve($expr);
        if (! is_string($filePath)) {
            return [];
        }

        $fileBaseName = (string) Strings::after($filePath, '/', -1);
        if (Strings::startsWith($fileBaseName, 'Skip')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $fileBaseName)];
    }

    /**
     * @return string[]
     */
    private function processMatchingFile(Expr $expr): array
    {
        $filePath = $this->nodeValueResolver->resolve($expr);
        if (! is_string($filePath)) {
            return [];
        }

        $fileBaseName = (string) Strings::after($filePath, '/', -1);
        if (! Strings::startsWith($fileBaseName, 'Skip')) {
            return [];
        }

        return [sprintf(self::INVERTED_ERROR_MESSAGE, $fileBaseName)];
    }
}
