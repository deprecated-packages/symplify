<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\UnusedTemplateRenderVariableResolver;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNetteRenderUnusedVariableRule\NoNetteRenderUnusedVariableRuleTest
 */
final class NoNetteRenderUnusedVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Missing "%s" variable that are not passed to the template';

    /**
     * @var TemplateRenderAnalyzer
     */
    private $templateRenderAnalyzer;

    /**
     * @var PathResolver
     */
    private $pathResolver;

    /**
     * @var UnusedTemplateRenderVariableResolver
     */
    private $unusedTemplateRenderVariableResolver;

    public function __construct(
        TemplateRenderAnalyzer $templateRenderAnalyzer,
        PathResolver $pathResolver,
        UnusedTemplateRenderVariableResolver $unusedTemplateRenderVariableResolver
    ) {
        $this->templateRenderAnalyzer = $templateRenderAnalyzer;
        $this->pathResolver = $pathResolver;
        $this->unusedTemplateRenderVariableResolver = $unusedTemplateRenderVariableResolver;
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
        if (! $this->templateRenderAnalyzer->isTemplateRenderMethodCall($node, $scope)) {
            return [];
        }

        if (count($node->args) < 2) {
            return [];
        }

        $firstArgValue = $node->args[0]->value;

        $resolvedTemplateFilePath = $this->pathResolver->resolveExistingFilePath($firstArgValue, $scope);
        if ($resolvedTemplateFilePath === null) {
            return [];
        }

        $unusedVariableNames = $this->unusedTemplateRenderVariableResolver->resolveMethodCallAndTemplate(
            $node,
            $resolvedTemplateFilePath,
            $scope
        );

        if ($unusedVariableNames === []) {
            return [];
        }

        $unusedPassedVariablesString = implode('", ', $unusedVariableNames);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $unusedPassedVariablesString);

        return [$errorMessage];
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
        $this->template->render(__DIR__ . '/some_file.latte');
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
        $this->template->render(__DIR__ . '/some_file.latte', [
            'existing_variable' => 'value'
        ]);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
