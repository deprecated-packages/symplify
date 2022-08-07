<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Privatization;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Privatization\NoPublicPropertyByTypeRule\NoPublicPropertyByTypeRuleTest
 */
final class NoPublicPropertyByTypeRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class cannot have public properties. Use getter/setters instead';

    /**
     * @param string[] $classTypes
     */
    public function __construct(
        private array $classTypes,
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isClassMatch($node)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if (! $this->hasClassPublicProperties($classLike)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
final class Person extends Entity
{
    public $name;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class Person extends Entity
{
    private $name;

    public function getName()
    {
        return $this->name;
    }
}
CODE_SAMPLE
                ,
                [
                    'classTypes' => ['Entity'],
                ]
            ),
        ]);
    }

    private function hasClassPublicProperties(Class_ $class): bool
    {
        foreach ($class->getProperties() as $property) {
            if ($property->isPublic()) {
                return true;
            }
        }

        return false;
    }

    private function isClassMatch(InClassNode $inClassNode): bool
    {
        $classReflection = $inClassNode->getClassReflection();
        if (! $classReflection->isClass()) {
            return false;
        }

        foreach ($this->classTypes as $classType) {
            if (! $classReflection->isSubclassOf($classType)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
