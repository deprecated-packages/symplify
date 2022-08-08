<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\TypeWithClassName;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenArrayMethodCallRule\ForbiddenArrayMethodCallRuleTest
 */
final class ForbiddenArrayMethodCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array method calls [$this, "method"] are not allowed. Use explicit method instead to help PhpStorm, PHPStan and Rector understand your code';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Array_::class;
    }

    /**
     * @param Array_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (count($node->items) !== 2) {
            return [];
        }

        $typeWithClassName = $this->resolveFirstArrayItemClassType($node, $scope);
        if (! $typeWithClassName instanceof TypeWithClassName) {
            return [];
        }

        $methodName = $this->resolveSecondArrayItemMethodName($node, $scope);
        if ($methodName === null) {
            return [];
        }

        // does method exist?
        if (! $typeWithClassName->hasMethod($methodName)->yes()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
usort($items, [$this, "method"]);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
usort($items, function (array $apples) {
    return $this->method($apples);
};
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveFirstArrayItemClassType(Array_ $array, Scope $scope): ?TypeWithClassName
    {
        $firstItem = $array->items[0];
        if (! $firstItem instanceof ArrayItem) {
            return null;
        }

        $firstItemType = $scope->getType($firstItem->value);
        if (! $firstItemType instanceof TypeWithClassName) {
            return null;
        }

        return $firstItemType;
    }

    private function resolveSecondArrayItemMethodName(Array_ $array, Scope $scope): ?string
    {
        $secondItem = $array->items[1];
        if (! $secondItem instanceof ArrayItem) {
            return null;
        }

        $secondItemValue = $secondItem->value;

        $secondItemType = $scope->getType($secondItemValue);
        if (! $secondItemType instanceof ConstantStringType) {
            return null;
        }

        return $secondItemType->getValue();
    }
}
