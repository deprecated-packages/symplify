<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node\NullableType;
use PhpParser\Node\Identifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNullableArrayPropertyRule\NoNullableArrayPropertyRuleTest
 */
final class NoNullableArrayPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use required typed property over of nullable property';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->type === null) {
            return [];
        }

        if ($node->type instanceof Identifier && $node->type->toString() !== 'array') {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private ?array $property = null;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private array $property;
}
CODE_SAMPLE
            ),
        ]);
    }
}
