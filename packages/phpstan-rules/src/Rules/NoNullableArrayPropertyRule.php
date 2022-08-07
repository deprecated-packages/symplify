<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNullableArrayPropertyRule\NoNullableArrayPropertyRuleTest
 */
final class NoNullableArrayPropertyRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use required typed property over of nullable array property';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Property::class;
    }

    /**
     * @param Property $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->type instanceof NullableType) {
            return [];
        }

        $typeName = $node->type->type->toString();
        if ($typeName !== 'array') {
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
    private array $property = [];
}
CODE_SAMPLE
            ),
        ]);
    }
}
