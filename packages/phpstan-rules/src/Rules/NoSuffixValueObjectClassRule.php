<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoSuffixValueObjectClassRule\NoSuffixValueObjectClassRuleTest
 */
final class NoSuffixValueObjectClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Value Object class name "%s" must be withotu "ValueObject" suffix. The correct class name is "%s".';

    /**
     * @see https://regex101.com/r/3jsBnt/1
     * @var string
     */
    private const VALUE_OBJECT_SUFFIX_REGEX = '#ValueObject$#';

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
        if (! property_exists($node, 'namespacedName')) {
            return [];
        }

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
        $errorMessage = sprintf(self::ERROR_MESSAGE, $shortClassName, $expectedShortClassName);

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeValueObject
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class Some
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasValueObjectNamespace(string $fullyQualifiedClassName): bool
    {
        return (bool) Strings::match($fullyQualifiedClassName, Regex::VALUE_OBJECT_REGEX);
    }

    private function hasValueObjectSuffix(string $fullyQualifiedClassName): bool
    {
        return (bool) Strings::match($fullyQualifiedClassName, self::VALUE_OBJECT_SUFFIX_REGEX);
    }
}
