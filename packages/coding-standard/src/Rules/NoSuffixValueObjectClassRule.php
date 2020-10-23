<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoSuffixValueObjectClassRule\NoSuffixValueObjectClassRuleTest
 */
final class NoSuffixValueObjectClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR = 'Value Object class name "%s" must be withotu "ValueObject" suffix. The correct class name is "%s".';

    /**
     * @see https://regex101.com/r/3jsBnt/1
     * @var string
     */
    private const VALUE_OBJECT_SUFFIX_REGEX = '#ValueObject$#';

    /**
     * @see https://regex101.com/r/zyZ9KJ/1
     * @var string
     */
    private const VALUE_OBJECT_NAMESPACE_REGEX = '#\bValueObject\b#';

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
        $namespacedName = $node->namespacedName;
        if (! $namespacedName instanceof Name) {
            return [];
        }

        $className = (string) $namespacedName;
        if ($className === '') {
            return [];
        }

        if (! $this->hasValueObjectNamespace($className)) {
            return [];
        }

        if (! $this->hasValueObjectSuffix($className)) {
            return [];
        }

        $shortClassName = (string) $node->name;
        $expectedShortClassName = Strings::replace($shortClassName, self::VALUE_OBJECT_SUFFIX_REGEX, '');
        $errorMessage = sprintf(self::ERROR, $shortClassName, $expectedShortClassName);

        return [$errorMessage];
    }

    private function hasValueObjectNamespace(string $fullyQualifiedClassName): bool
    {
        return (bool) Strings::match($fullyQualifiedClassName, self::VALUE_OBJECT_NAMESPACE_REGEX);
    }

    private function hasValueObjectSuffix(string $fullyQualifiedClassName): bool
    {
        return (bool) Strings::match($fullyQualifiedClassName, self::VALUE_OBJECT_SUFFIX_REGEX);
    }
}
