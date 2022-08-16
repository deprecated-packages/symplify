<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\CheckAttributteArgumentClassExistsRuleTest
 *
 * @implements Rule<AttributeGroup>
 */
final class CheckAttributteArgumentClassExistsRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class was not found';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getNodeType(): string
    {
        return AttributeGroup::class;
    }

    /**
     * @param AttributeGroup $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->attrs as $attribute) {
            foreach ($attribute->args as $arg) {
                $value = $arg->value;
                if (! $this->isClassConstFetch($value)) {
                    continue;
                }

                $valueType = $scope->getType($value);
                if (! $valueType instanceof ConstantStringType) {
                    $ruleErrors[] = self::ERROR_MESSAGE;
                    continue;
                }

                if ($this->reflectionProvider->hasClass($valueType->getValue())) {
                    continue;
                }

                $ruleErrors[] = self::ERROR_MESSAGE;
            }
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
#[SomeAttribute(firstName: 'MissingClass::class')]
class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
#[SomeAttribute(firstName: ExistingClass::class)]
class SomeClass
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isClassConstFetch(Expr $expr): bool
    {
        if (! $expr instanceof ClassConstFetch) {
            return false;
        }

        if (! $expr->name instanceof Identifier) {
            return false;
        }

        return $expr->name->toString() === 'class';
    }
}
