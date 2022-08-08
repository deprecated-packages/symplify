<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Enum\AttributeKey;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\ForbiddenSameNamedNewInstanceRuleTest
 * @implements Rule<Expression>
 */
final class ForbiddenSameNamedNewInstanceRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'New objects with "%s" name are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$product = new Product();
$product = new Product();

$this->productRepository->save($product);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$firstProduct = new Product();
$secondProduct = new Product();

$this->productRepository->save($firstProduct);
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Expression::class;
    }

    /**
     * @param Expression $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof Assign) {
            return [];
        }

        if ($this->shouldSkip($scope, $node)) {
            return [];
        }

        $assign = $node->expr;

        if (! $assign->var instanceof Variable) {
            return [];
        }

        if (! $assign->expr instanceof New_) {
            return [];
        }

        if (! is_string($assign->var->name)) {
            return [];
        }

        // is type already defined?
        $variableName = $assign->var->name;
        if (! $scope->hasVariableType($variableName)->yes()) {
            return [];
        }

        $variableType = $scope->getVariableType($variableName);
        if ($variableType instanceof NullType) {
            return [];
        }

        $exprType = $scope->getType($node->expr);
        if (! $exprType instanceof ObjectType) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, '$' . $variableName);
        return [$errorMessage];
    }

    private function shouldSkip(Scope $scope, Expression $expression): bool
    {
        if ($this->isNestedInForeach($expression)) {
            return true;
        }

        $classReflection = $scope->getClassReflection();

        // skip tests, are easier to re-use variable there
        return $classReflection instanceof ClassReflection && $classReflection->isSubclassOf(TestCase::class);
    }

    private function isNestedInForeach(Expression $expression): bool
    {
        // only available on stmt
        $parentStmtTypes = $expression->getAttribute(AttributeKey::PARENT_STMT_TYPES);
        if (! is_array($parentStmtTypes)) {
            return false;
        }

        // skip in foreach, as nesting might be on purpose
        return in_array(Foreach_::class, $parentStmtTypes, true);
    }
}
