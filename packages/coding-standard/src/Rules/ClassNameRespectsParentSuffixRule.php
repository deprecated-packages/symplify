<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ClassNameRespectsParentSuffixRule\ClassNameRespectsParentSuffixRuleTest
 */
final class ClassNameRespectsParentSuffixRule implements Rule
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

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name === null) {
            return [];
        }

        if ($node->extends) {
            return $this->processParent($node, $node->extends);
        }

        $class = (string) $node->name;
        foreach ($node->implements as $implement) {
            $errorMessages = $this->processClassNameAndShort($class, $implement->getLast());
            if ($errorMessages !== []) {
                return $errorMessages;
            }
        }

        return [];
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
        $className = (string) $class->name;

        $parentShortClassName = $parentClassName->getLast();
        $parentShortClassName = $this->resolveExpectedSuffix($parentShortClassName);

        return $this->processClassNameAndShort($className, $parentShortClassName);
    }

    private function processClassNameAndShort(string $className, string $determiningShortClassName): array
    {
        $determiningShortClassName = $this->resolveExpectedSuffix($determiningShortClassName);

        foreach ($this->getParentClassesToCheck() as $parentClass) {
            if (! Strings::endsWith($parentClass, $determiningShortClassName)) {
                continue;
            }

            if (Strings::endsWith($className, $determiningShortClassName)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $className, $determiningShortClassName);

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
