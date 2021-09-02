<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template\UnusedTwigTemplateVariableAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigRenderUnusedVariableRule\NoTwigRenderUnusedVariableRuleTest
 */
final class NoTwigRenderUnusedVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Passed "%s" variable that are not used in the template';

    public function __construct(
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private PathResolver $pathResolver,
        private UnusedTwigTemplateVariableAnalyzer $unusedTwigTemplateVariableAnalyzer
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
        if (! $this->templateRenderAnalyzer->isTwigRenderMethodCall($node, $scope)) {
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

        $unusedVariableNames = $this->unusedTwigTemplateVariableAnalyzer->resolveMethodCallAndTemplate(
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
$environment = new Twig\Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'used_variable' => 'value'
]);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$environment = new Twig\Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'unused_variable' => 'value'
]);
CODE_SAMPLE
            ),
        ]);
    }
}
