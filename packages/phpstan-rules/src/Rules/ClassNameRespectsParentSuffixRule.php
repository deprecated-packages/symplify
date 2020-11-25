<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule\ClassNameRespectsParentSuffixRuleTest
 */
final class ClassNameRespectsParentSuffixRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" should have suffix "%s" by parent class/interface';

    /**
     * @var string[]
     */
    private const DEFAULT_PARENT_CLASSES = [
        'Command',
        'Controller',
        'Repository',
        'Presenter',
        'Request',
        'Response',
        'EventSubscriber',
        'EventSubscriberInterface',
        'FixerInterface',
        'Sniff',
        'FixerInterface',
        'Handler',
        'Rule',
        'TestCase' => 'Test',
    ];

    /**
     * @var string[]
     */
    private $parentClasses = [];

    /**
     * @param string[] $parentClasses
     */
    public function __construct(array $parentClasses = [])
    {
        $this->parentClasses = $parentClasses;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $shortClassName = $node->name;
        if ($shortClassName === null) {
            return [];
        }

        if ($node->isAbstract()) {
            return [];
        }

        if ($node->extends !== null) {
            return $this->processParent($node, $node->extends);
        }

        $class = (string) $shortClassName;
        foreach ($node->implements as $implement) {
            $errorMessages = $this->processClassNameAndShort($class, $implement->getLast());
            if ($errorMessages !== []) {
                return $errorMessages;
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class Some extends Command
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeCommand extends Command
{
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * - SomeInterface => Some
     * - SomeAbstract => Some
     * - AbstractSome => Some
     */
    private function resolveExpectedSuffix(string $parentType): string
    {
        if (Strings::endsWith($parentType, 'Interface')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Interface'));
        }

        if (Strings::endsWith($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Abstract'));
        }

        if (Strings::startsWith($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, strlen('Abstract'));
        }

        return $parentType;
    }

    /**
     * @return string[]
     */
    private function processParent(Class_ $class, Name $parentClassName): array
    {
        $shortClassName = (string) $class->name;

        $parentShortClassName = $parentClassName->getLast();
        $parentShortClassName = $this->resolveExpectedSuffix($parentShortClassName);

        return $this->processClassNameAndShort($shortClassName, $parentShortClassName);
    }

    /**
     * @return array<int, string>
     */
    private function processClassNameAndShort(string $class, string $currentShortClass): array
    {
        $currentShortClass = $this->resolveExpectedSuffix($currentShortClass);
        $parentClassesToCheck = $this->getParentClassesToCheck();
        foreach ($parentClassesToCheck as $parentSuffix => $expectedSuffix) {
            if (is_int($parentSuffix)) {
                $parentSuffix = $expectedSuffix;
            }

            if (! Strings::endsWith($currentShortClass, $parentSuffix)) {
                continue;
            }

            if (Strings::endsWith($class, $expectedSuffix)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $class, $currentShortClass);
            return [$errorMessage];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getParentClassesToCheck(): array
    {
        return array_merge(self::DEFAULT_PARENT_CLASSES, $this->parentClasses);
    }
}
