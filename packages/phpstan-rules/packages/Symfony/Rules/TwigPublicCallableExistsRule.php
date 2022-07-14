<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Symfony\ValueObject\TwoExprs;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Twig\Extension\AbstractExtension;

/**
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\TwigPublicCallableExistsRule\TwigPublicCallableExistsRuleTest
 */
final class TwigPublicCallableExistsRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The callable method [$this, "%s"] was not found';

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
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isSubclassOf(AbstractExtension::class)) {
            return [];
        }

        $twoExprs = $this->matchTwoExprs($node);
        if (! $twoExprs instanceof TwoExprs) {
            return [];
        }

        if (! $this->isThisVariable($twoExprs->getFirstExpr())) {
            return [];
        }

        $secondExpr = $twoExprs->getSecondExpr();
        if (! $secondExpr instanceof String_) {
            return [];
        }

        $methodName = $secondExpr->value;
        if ($classReflection->hasMethod($methodName)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction;

final class TwigExtensionWithMissingCallable extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('someFunctionName', [$this, 'someMethod']),
        ];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction;

final class TwigExtensionWithMissingCallable extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('someFunctionName', [$this, 'someMethod']),
        ];
    }

    public function someMethod()
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function matchTwoExprs(Array_ $array): ?TwoExprs
    {
        if (count($array->items) !== 2) {
            return null;
        }

        $firstItem = $array->items[0];
        if (! $firstItem instanceof ArrayItem) {
            return null;
        }

        if (! $firstItem->value instanceof Variable) {
            return null;
        }

        $firstExpr = $firstItem->value;

        $secondItem = $array->items[1];
        if (! $secondItem instanceof ArrayItem) {
            return null;
        }

        $secondExpr = $secondItem->value;

        return new TwoExprs($firstExpr, $secondExpr);
    }

    private function isThisVariable(Expr $expr): bool
    {
        if (! $expr instanceof Variable) {
            return false;
        }

        return $expr->name === 'this';
    }
}
