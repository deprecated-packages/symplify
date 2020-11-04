<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use SplFixedArray;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\NoArrayAccessOnObjectRuleTest
 */
final class NoArrayAccessOnObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit methods over array access on object';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $varStaticType = $scope->getType($node->var);
        if (! $varStaticType instanceof TypeWithClassName) {
            return [];
        }

        if (is_a($varStaticType->getClassName(), SplFixedArray::class, true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject->getExplicitValue();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
