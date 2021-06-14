<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNetteDoubleTemplateAssignRule\NoNetteDoubleTemplateAssignRuleTest
 */
final class NoNetteDoubleTemplateAssignRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid double template variable override of "%s"';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder,
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! is_a($classReflection->getName(), 'Nette\Application\UI\Presenter', true)) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($node, Assign::class);

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
        if (! $this->simpleNameResolver->isName($nestedPropertyFetch->var, 'this')) {
            return false;
        }

        return $this->simpleNameResolver->isName($nestedPropertyFetch->name, 'template');
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

            $variableName = $this->simpleNameResolver->getName($templatePropertyFetch->name);
            if ($variableName === null) {
                continue;
            }

            $parentScopeNode = $this->simpleNodeFinder->findFirstParentByTypes(
                $assign,
                [ClassMethod::class, If_::class, Else_::class, ElseIf_::class]
            );
            if (! $parentScopeNode instanceof ClassMethod) {
                continue;
            }

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
}
