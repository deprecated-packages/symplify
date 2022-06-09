<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\ForbiddenSameNamedNewInstanceRuleTest
 * @implements Rule<Assign>
 */
final class ForbiddenSameNamedNewInstanceRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'New objects with "%s" name are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

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
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();

        // skip tests, are easier to re-use variable there
        if ($classReflection instanceof ClassReflection && $classReflection->isSubclassOf(TestCase::class)) {
            return [];
        }

        if (! $node->var instanceof Variable) {
            return [];
        }

        if (! $node->expr instanceof New_) {
            return [];
        }

        // is type already defined?
        $variableName = $this->simpleNameResolver->getName($node->var);
        if (! $scope->hasVariableType($variableName)->yes()) {
            return [];
        }

        $exprType = $scope->getType($node->expr);
        if (! $exprType instanceof ObjectType) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, '$' . $variableName);
        return [$errorMessage];
    }
}
