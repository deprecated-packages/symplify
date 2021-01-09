<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

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
        if (! $node->type instanceof NullableType) {
            return [];
        }

        $type = $node->type->type;
        if (! $this->simpleNameResolver->isName($type, 'array')) {
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
