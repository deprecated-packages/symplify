<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Nette\Rules\RequireTemplateInNetteControlRule\RequireTemplateInNetteControlRuleTest
 */
final class RequireTemplateInNetteControlRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Set control template explicitly in $this->template->setFile(...) or $this->template->render(...)';

    public function __construct(
        private NodeFinder $nodeFinder,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (! $classReflection->isSubclassOf('Nette\Application\UI\Control')) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        foreach ($classLike->getMethods() as $classMethod) {
            $classMethodName = $classMethod->name->toString();
            if ($classMethodName !== 'render' && ! str_starts_with($classMethodName, 'render')) {
                continue;
            }

            if ($this->hasTemplateSet($classMethod)) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($classMethod->getLine())
                ->build();

            return [$ruleError];
        }

        return [];
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

    private function hasTemplateSet(ClassMethod $classMethod): bool
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($classMethod, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $methodCall->name instanceof Identifier) {
                continue;
            }

            $methodCallName = $methodCall->name->toString();
            if (! in_array($methodCallName, ['setFile', 'render'], true)) {
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
