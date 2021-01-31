<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Application\UI\Control;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireTemplateInNetteControlRule\RequireTemplateInNetteControlRuleTest
 */
final class RequireTemplateInNetteControlRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Set control template explicitly in $this->template->setFile(...) or $this->template->render(...)';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(SimpleNameResolver $simpleNameResolver, NodeFinder $nodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
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
        if (! $this->isInControlClass($scope)) {
            return [];
        }

        if (! $this->simpleNameResolver->isNames($node, ['render', 'render*'])) {
            return [];
        }

        $hasTemplateSet = $this->hasTemplateSet($node);
        if ($hasTemplateSet) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render('some_file.latte');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isInControlClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return is_a($classReflection->getName(), Control::class, true);
    }

    private function hasTemplateSet(ClassMethod $classMethod): bool
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($classMethod, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $this->simpleNameResolver->isNames($methodCall->name, ['setFile', 'render'])) {
                continue;
            }

            if (! isset($methodCall->args[0])) {
                continue;
            }

            return true;
        }

        return false;
    }
}
