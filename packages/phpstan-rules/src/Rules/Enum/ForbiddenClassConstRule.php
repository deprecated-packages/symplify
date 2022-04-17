<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use Symplify\PHPStanRules\Enum\EnumConstantAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\ClassAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\ForbiddenClassConstRuleTest
 */
final class ForbiddenClassConstRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constants in this class are not allowed, move them to custom Enum class instead';

    /**
     * @param array<class-string> $classTypes
     */
    public function __construct(
        private ClassAnalyzer $classAnalyzer,
        private EnumConstantAnalyzer $enumConstantAnalyzer,
        private array $classTypes
    ) {
    }

    /**
     * @return class-string<Node>
     */
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
        $classLike = $node->getOriginalNode();
        if ($classLike->getConstants() === []) {
            return [];
        }

        if (! $this->isInClassTypes($node, $this->classTypes)) {
            return [];
        }

        $constantNames = $this->classAnalyzer->resolveConstantNames($classLike);

        foreach ($constantNames as $constantName) {
            if ($this->enumConstantAnalyzer->isNonEnumConstantName($constantName)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
final class Product extends AbstractEntity
{
    public const TYPE_HIDDEN = 0;

    public const TYPE_VISIBLE = 1;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class Product extends AbstractEntity
{
}

class ProductVisibility extends Enum
{
    public const HIDDEN = 0;

    public const VISIBLE = 1;
}
CODE_SAMPLE
                ,
                [
                    'classTypes' => ['AbstractEntity'],
                ]
            ),
        ]);
    }

    /**
     * @param class-string[] $classTypes
     */
    private function isInClassTypes(InClassNode $inClassNode, array $classTypes): bool
    {
        $classReflection = $inClassNode->getClassReflection();

        foreach ($classTypes as $classType) {
            if ($classReflection->isSubclassOf($classType)) {
                return true;
            }
        }

        return false;
    }
}
