<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteTemplateVariableReadRule\NoNetteTemplateVariableReadRuleTest
 * @implements Rule<PropertyFetch>
 */
final class NoNetteTemplateVariableReadRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid "$this->template->%s" for read access, as it can be defined anywhere. Use local "$%s" variable instead';

    public function __construct(
        private NetteTypeAnalyzer $netteTypeAnalyzer
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }

    /**
     * @param PropertyFetch $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isThisPropertyFetch($node->var, 'template')) {
            return [];
        }

        if (! $this->netteTypeAnalyzer->isInsideComponentContainer($scope)) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() === 'flashes') {
            return [];
        }

        $assignedToVar = $node->getAttribute(AttributeKey::ASSIGNED_TO);
        if ($assignedToVar instanceof Expr && $this->isPayloadAjaxJuggling($assignedToVar)) {
            return [];
        }

        if ($scope->isInExpressionAssign($node)) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $templateVariableName = $node->name->toString();

        $errorMessage = sprintf(self::ERROR_MESSAGE, $templateVariableName, $templateVariableName);
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
        if ($this->template->key === 'value') {
            return;
        }
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
        $this->template->key = 'value';
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isThisPropertyFetch(Expr $expr, string $propertyName): bool
    {
        if (! $expr instanceof PropertyFetch) {
            return false;
        }

        if (! $expr->var instanceof Variable) {
            return false;
        }

        if (! is_string($expr->var->name)) {
            return false;
        }

        if ($expr->var->name !== 'this') {
            return false;
        }

        if (! $expr->name instanceof Identifier) {
            return false;
        }

        return $expr->name->toString() === $propertyName;
    }

    private function isPayloadAjaxJuggling(Expr $expr): bool
    {
        if (! $expr instanceof PropertyFetch) {
            return false;
        }

        return $this->isThisPropertyFetch($expr->var, 'payload');
    }
}
