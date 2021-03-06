<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\MissingTemplateRenderVariableResolver;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNetteRenderMissingVariableRule\NoNetteRenderMissingVariableRuleTest
 */
final class NoNetteRenderMissingVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Passed "%s" variable that are not used in the template';

    /**
     * @var TemplateRenderAnalyzer
     */
    private $templateRenderAnalyzer;

    /**
     * @var PathResolver
     */
    private $pathResolver;

    /**
     * @var MissingTemplateRenderVariableResolver
     */
    private $missingTemplateRenderVariableResolver;

    public function __construct(
        TemplateRenderAnalyzer $templateRenderAnalyzer,
        PathResolver $pathResolver,
        MissingTemplateRenderVariableResolver $missingTemplateRenderVariableResolver
    ) {
        $this->templateRenderAnalyzer = $templateRenderAnalyzer;
        $this->pathResolver = $pathResolver;
        $this->missingTemplateRenderVariableResolver = $missingTemplateRenderVariableResolver;
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

        if (count($node->args) < 1) {
            return [];
        }

        $firstArgValue = $node->args[0]->value;

        $resolvedTemplateFilePath = $this->pathResolver->resolveExistingFilePath($firstArgValue, $scope);
        if ($resolvedTemplateFilePath === null) {
            return [];
        }

        $missingVariableNames = $this->missingTemplateRenderVariableResolver->resolveFromTemplateAndMethodCall(
            $node,
            $resolvedTemplateFilePath,
            $scope
        );

        if ($missingVariableNames === []) {
            return [];
        }

        $unusedPassedVariablesString = implode('", "', $missingVariableNames);
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
        $this->template->render(__DIR__ . '/some_file.latte', [
            'non_existing_variable' => 'value'
        ]);
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
