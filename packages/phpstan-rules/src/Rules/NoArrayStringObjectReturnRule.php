<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\NoArrayStringObjectReturnRuleTest
 */
final class NoArrayStringObjectReturnRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use another value object over array with string-keys and objects, array<string, ValueObject>';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Return_::class];
    }

    /**
     * @param Return_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->expr === null) {
            return [];
        }

        // skip func call that only delegates
        if ($node->expr instanceof FuncCall) {
            return [];
        }

        $returnedExprType = $scope->getType($node->expr);
        if (! $this->isArrayStringToObjectType($returnedExprType)) {
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
    public getItems()
    {
        return $this->getValues();
    }

    /**
     * @return array<string, Value>
     */
    private function getValues()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public getItems()
    {
        return $this->getValues();
    }

    /**
     * @return WrappingValue[]
     */
    private function getValues()
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isArrayStringToObjectType(Type $type): bool
    {
        if (! $type instanceof ArrayType) {
            return false;
        }

        if (! $type->getKeyType() instanceof StringType) {
            return false;
        }

        return $type->getItemType() instanceof ObjectType;
    }
}
