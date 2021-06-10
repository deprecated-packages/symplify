<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\ForbiddenClassConstRuleTest
 */
final class ForbiddenClassConstRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constants in this class are not allowed, move them to custom Enum class instead';

    /**
     * @var class-string[]
     */
    private $classTypes = [];

    /**
     * @param array<class-string> $classTypes
     */
    public function __construct(array $classTypes)
    {
        $this->classTypes = $classTypes;
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
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if ($classLike->getConstants() === []) {
            return [];
        }

        if (! $this->isInClassTypes($scope, $this->classTypes)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
    private function isInClassTypes(Scope $scope, array $classTypes): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classTypes as $classType) {
            if ($classReflection->isSubclassOf($classType)) {
                return true;
            }
        }

        return false;
    }
}
