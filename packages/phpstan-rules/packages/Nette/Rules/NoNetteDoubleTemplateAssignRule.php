<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteDoubleTemplateAssignRule\NoNetteDoubleTemplateAssignRuleTest
 */
final class NoNetteDoubleTemplateAssignRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid double template variable override of "%s"';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isSubclassOf('Nette\Application\UI\Presenter')) {
            return [];
        }

        // work only with single root assigns, as nesting can be hard to handle
        $assigns = $this->findFirstLevelAssigns($node);

        $duplicatedVariableNames = $this->resolveDuplicatedTemplateVariableNames($assigns);
        if ($duplicatedVariableNames === []) {
            return [];
        }

        $variableNamesString = implode('", "', $duplicatedVariableNames);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $variableNamesString);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        $this->template->key = '1';
        $this->template->key = '2';
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        $this->template->key = '2';
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isThisTemplatePropertyFetch(PropertyFetch $propertyFetch): bool
    {
        if (! $propertyFetch->var instanceof PropertyFetch) {
            return false;
        }

        $nestedPropertyFetch = $propertyFetch->var;
        if (! $this->isVariableName($nestedPropertyFetch->var, 'this')) {
            return false;
        }

        if (! $nestedPropertyFetch->name instanceof Identifier) {
            return false;
        }

        return $nestedPropertyFetch->name->toString() === 'template';
    }

    /**
     * @param Assign[] $assigns
     * @return string[]
     */
    private function resolveDuplicatedTemplateVariableNames(array $assigns): array
    {
        $assignedTemplateVariableNames = [];
        $duplicatedTemplateNames = [];

        foreach ($assigns as $assign) {
            $templatePropertyFetch = $this->matchTemplatePropertyFetch($assign);
            if (! $templatePropertyFetch instanceof PropertyFetch) {
                continue;
            }

            if (! $templatePropertyFetch->name instanceof Identifier) {
                continue;
            }

            $variableName = $templatePropertyFetch->name->toString();
            if (in_array($variableName, $assignedTemplateVariableNames, true)) {
                $duplicatedTemplateNames[] = $variableName;
            }

            $assignedTemplateVariableNames[] = $variableName;
        }

        return array_unique($duplicatedTemplateNames);
    }

    private function matchTemplatePropertyFetch(Assign $assign): ?PropertyFetch
    {
        $assignedVar = $assign->var;
        if (! $assignedVar instanceof PropertyFetch) {
            return null;
        }

        if (! $this->isThisTemplatePropertyFetch($assignedVar)) {
            return null;
        }

        return $assignedVar;
    }

    /**
     * @return Assign[]
     */
    private function findFirstLevelAssigns(ClassMethod $classMethod): array
    {
        $assigns = [];

        foreach ((array) $classMethod->stmts as $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            if (! $stmt->expr instanceof Assign) {
                continue;
            }

            $assigns[] = $stmt->expr;
        }

        return $assigns;
    }

    private function isVariableName(Expr $expr, string $variableName): bool
    {
        if (! $expr instanceof Variable) {
            return false;
        }

        if (! is_string($expr->name)) {
            return false;
        }

        return $expr->name === $variableName;
    }
}
