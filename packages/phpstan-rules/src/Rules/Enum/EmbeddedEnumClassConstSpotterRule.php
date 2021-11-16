<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use Symplify\PHPStanRules\Enum\EnumConstantAnalyzer;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\Matcher\SharedNamePrefixMatcher;
use Symplify\PHPStanRules\NodeAnalyzer\ClassAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\EmbeddedEnumClassConstSpotterRuleTest
 */
final class EmbeddedEnumClassConstSpotterRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constants "%s" should be extract to standalone enum class';

    /**
     * @param array<class-string> $parentTypes
     */
    public function __construct(
        private ClassAnalyzer $classAnalyzer,
        private SharedNamePrefixMatcher $sharedNamePrefixMatcher,
        private EnumConstantAnalyzer $enumConstantAnalyzer,
        private array $parentTypes
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [InClassNode::class];
    }

    /**
     * @param InClassNode $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($scope)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $constantNames = $this->classAnalyzer->resolveConstantNames($classLike);

        $groupedByPrefix = $this->sharedNamePrefixMatcher->match($constantNames);

        $enumConstantNamesGroup = [];

        foreach ($groupedByPrefix as $prefix => $constantNames) {
            if (\count($constantNames) < 1) {
                continue;
            }

            if ($this->enumConstantAnalyzer->isNonEnumConstantPrefix($prefix)) {
                continue;
            }

            $enumConstantNamesGroup[] = $constantNames;
        }

        return $this->createErrorMessages($enumConstantNamesGroup);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new ConfiguredCodeSample(
            <<<'CODE_SAMPLE'
class SomeProduct extends AbstractObject
{
    public const STATUS_ENABLED = 1;

    public const STATUS_DISABLED = 0;
}
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
class SomeProduct extends AbstractObject
{
}

class SomeStatus
{
    public const ENABLED = 1;

    public const DISABLED = 0;
}
CODE_SAMPLE
            ,
            [
                'parentTypes' => ['AbstractObject'],
            ]
        )]);
    }

    private function shouldSkip(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        // already enum
        if (\str_contains($classReflection->getName(), '\\Enum\\') && ! \str_contains(
            $classReflection->getName(),
            '\\Rules\\Enum\\'
        )) {
            return true;
        }

        foreach ($this->parentTypes as $parentType) {
            if ($classReflection->isSubclassOf($parentType)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[][] $enumConstantNamesGroup
     * @return string[]
     */
    private function createErrorMessages(array $enumConstantNamesGroup): array
    {
        $errorMessages = [];

        foreach ($enumConstantNamesGroup as $enumConstantNameGroup) {
            $enumConstantNamesString = \implode('", "', $enumConstantNameGroup);
            $errorMessages[] = \sprintf(self::ERROR_MESSAGE, $enumConstantNamesString);
        }

        return $errorMessages;
    }
}
