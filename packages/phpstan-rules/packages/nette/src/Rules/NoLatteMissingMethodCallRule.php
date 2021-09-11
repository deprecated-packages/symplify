<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Unset_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Nette\Latte\LatteTemplateMacroAnalyzer;
use Symplify\PHPStanRules\Nette\Latte\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\NoLatteMissingMethodCallRule\NoLatteMissingMethodCallRuleTest
 */
final class NoLatteMissingMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "%s" of type "%s" does not have "%s()" method';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private PathResolver $pathResolver,
        private LatteTemplateMacroAnalyzer $latteTemplateMacroAnalyzer,
        private LatteToPhpCompiler $latteToPhpCompiler
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->templateRenderAnalyzer->isNetteTemplateRenderMethodCall($node, $scope)) {
            return [];
        }

        // must be template path + variables
        if (count($node->args) !== 2) {
            return [];
        }

        $firstArgValue = $node->args[0]->value;

        $resolvedTemplateFilePath = $this->pathResolver->resolveExistingFilePath($firstArgValue, $scope);
        if ($resolvedTemplateFilePath === null) {
            return [];
        }

        // nothing we can do - nested templates - @todo possibly improve for included/excluded files with known paths
        if ($this->latteTemplateMacroAnalyzer->hasMacros($resolvedTemplateFilePath, ['include', 'extends'])) {
            return [];
        }

        $phpContent = $this->latteToPhpCompiler->compileFilePath($resolvedTemplateFilePath);

        dump($phpContent);
        dump('___@todo');
        die;

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
        // @todo
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
        // @todo
    }
}
CODE_SAMPLE
            ),
        ]);
    }

//    private function isThisPropertyFetch(Expr $expr, string $propertyName): bool
//    {
//        if (! $expr instanceof PropertyFetch) {
//            return false;
//        }
//
//        if (! $this->simpleNameResolver->isName($expr->var, 'this')) {
//            return false;
//        }
//
//        return $this->simpleNameResolver->isName($expr->name, $propertyName);
//    }
//
//    private function shouldSkip(Node $parentNode, PropertyFetch $propertyFetch): bool
//    {
//        if ($parentNode instanceof Unset_) {
//            return true;
//        }
//
//        // flashes are allowed
//        if ($this->simpleNameResolver->isNames($propertyFetch->name, ['flashes'])) {
//            return true;
//        }
//
//        // payload ajax juggling
//        // is: $this->payload->xyz = $this->template->xyz
//        return $this->isPayloadAjaxJuggling($parentNode);
}
