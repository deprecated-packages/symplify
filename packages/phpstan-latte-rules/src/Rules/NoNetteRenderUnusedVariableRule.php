<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\LattePHPStanCompiler\NodeAnalyzer\UnusedNetteTemplateRenderVariableResolver;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanLatteRules\Tests\Rules\NoNetteRenderUnusedVariableRule\NoNetteRenderUnusedVariableRuleTest
 */
final class NoNetteRenderUnusedVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Extra variables "%s" are passed to the template but never used there';

    public function __construct(
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private PathResolver $pathResolver,
        private UnusedNetteTemplateRenderVariableResolver $unusedNetteTemplateRenderVariableResolver,
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

        if (count($node->args) < 2) {
            return [];
        }

        $firstArgOrVariadicPlaceholder = $node->args[0];
        if (! $firstArgOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        $firstArgValue = $firstArgOrVariadicPlaceholder->value;

        $templateFilePaths = $this->pathResolver->resolveExistingFilePaths($firstArgValue, $scope, 'latte');
        if ($templateFilePaths === []) {
            return [];
        }

        $unusedVariableNamesByTemplateFilePath = [];

        foreach ($templateFilePaths as $templateFilePath) {
            $unusedVariableNamesByTemplateFilePath[] = $this->unusedNetteTemplateRenderVariableResolver->resolveMethodCallAndTemplate(
                $node,
                $templateFilePath,
                $scope
            );
        }

        $everywhereUnusedVariableNames = array_intersect(...$unusedVariableNamesByTemplateFilePath);
        if ($everywhereUnusedVariableNames === []) {
            return [];
        }

        $unusedPassedVariablesString = implode('", "', $everywhereUnusedVariableNames);
        $error = sprintf(self::ERROR_MESSAGE, $unusedPassedVariablesString);

        return [$error];
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
            'never_used_in_template' => 'value'
        ]);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
