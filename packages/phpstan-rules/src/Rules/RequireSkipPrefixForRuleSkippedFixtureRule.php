<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Yield_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Testing\RuleTestCase;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
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
    public const ERROR_MESSAGE = 'Skipped tested file must start with "Skip" prefix';

    /**
     * @var string
     */
    public const INVERTED_ERROR_MESSAGE = 'File with error cannot start with "Skip" prefix';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NodeValueResolver $nodeValueResolver,
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Yield_::class];
    }

    /**
     * @param Yield_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->value instanceof Array_) {
            return [];
        }

        // is yield array in test?
        if ($this->shouldSkipClassName($scope)) {
            return [];
        }

        $array = $node->value;

        if (count($array->items) !== 2) {
            return [];
        }

        $firstItem = $array->items[0];
        if (! $firstItem instanceof ArrayItem) {
            return [];
        }

        $shortFilePath = $this->resolveShortFileName($firstItem, $scope);
        if ($shortFilePath === null) {
            return [];
        }

        $secondItem = $array->items[1];
        if (! $secondItem instanceof ArrayItem) {
            return [];
        }

        if ($this->isEmptyArray($secondItem->value)) {
            return $this->processSkippedFile($shortFilePath);
        }

        return $this->processMatchingFile($shortFilePath);
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
     * @param array<string|int> $expectedErrorMessagesWithLines
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
     * @param array<string|int> $expectedErrorMessagesWithLines
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
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return true;
        }

        // in tests, there should be no nested test, that would be run by PHPUnit
        if (StaticPHPUnitEnvironment::isPHPUnitRun()) {
            return ! Strings::endsWith($className, 'Test');
        }

        return ! is_a($className, RuleTestCase::class, true);
    }

    private function isEmptyArray(Expr $expr): bool
    {
        if (! $expr instanceof Array_) {
            return false;
        }

        return $expr->items === [];
    }

    /**
     * @return string[]
     */
    private function processSkippedFile(string $shortFileName): array
    {
        if (Strings::startsWith($shortFileName, 'Skip')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    private function processMatchingFile(string $shortFileName): array
    {
        if (! Strings::startsWith($shortFileName, 'Skip')) {
            return [];
        }

        return [self::INVERTED_ERROR_MESSAGE];
    }

    private function resolveShortFileName(ArrayItem $arrayItem, Scope $scope): ?string
    {
        /** @var Concat[] $concats */
        $concats = $this->nodeFinder->findInstanceOf($arrayItem, Concat::class);

        foreach ($concats as $concat) {
            $resolvedValue = $this->nodeValueResolver->resolve($concat, $scope->getFile());
            if (! is_string($resolvedValue)) {
                continue;
            }

            return Strings::after($resolvedValue, DIRECTORY_SEPARATOR, -1);
        }

        return null;
    }
}
