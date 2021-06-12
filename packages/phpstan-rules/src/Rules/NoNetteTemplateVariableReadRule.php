<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Unset_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\NetteTypeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNetteTemplateVariableReadRule\NoNetteTemplateVariableReadRuleTest
 */
final class NoNetteTemplateVariableReadRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid $this->template->variable for read access, as it can be defined anywhere. Use local $variable instead';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param PropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->netteTypeAnalyzer->isInsideComponentContainer($scope)) {
            return [];
        }

        if (! $this->isThisPropertyFetch($node->var, 'template')) {
            return [];
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof Assign && $parent->var === $node) {
            return [];
        }

        if ($parent === []) {
            return [];
        }

        if ($this->shouldSkip($parent, $node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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

        if (! $this->simpleNameResolver->isName($expr->var, 'this')) {
            return false;
        }

        return $this->simpleNameResolver->isName($expr->name, $propertyName);
    }

    private function shouldSkip(Node $parentNode, PropertyFetch $propertyFetch): bool
    {
        if ($parentNode instanceof Unset_) {
            return true;
        }

        // flashes are allowed
        if ($this->simpleNameResolver->isNames($propertyFetch->name, ['flashes'])) {
            return true;
        }

        // payload ajax juggling
        // is: $this->payload->xyz = $this->template->xyz
        return $this->isPayloadAjaxJuggling($parentNode);
    }

    private function isPayloadAjaxJuggling(Node $node): bool
    {
        if (! $node instanceof Assign) {
            return false;
        }

        if (! $node->var instanceof PropertyFetch) {
            return false;
        }

        $propertyFetch = $node->var;
        return $this->isThisPropertyFetch($propertyFetch->var, 'payload');
    }
}
