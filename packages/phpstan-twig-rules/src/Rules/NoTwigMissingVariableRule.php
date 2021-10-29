<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanTwigRules\NodeAnalyzer\SymfonyRenderWithParametersMatcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\TwigPHPStanCompiler\NodeAnalyzer\MissingTwigTemplateRenderVariableResolver;

/**
 * @api
 * @see \Symplify\PHPStanTwigRules\Tests\Rules\NoTwigMissingVariableRule\NoTwigMissingVariableRuleTest
 */
final class NoTwigMissingVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "%s" is used in template but missing in render() method';

    public function __construct(
        private MissingTwigTemplateRenderVariableResolver $missingTwigTemplateRenderVariableResolver,
        private SymfonyRenderWithParametersMatcher $symfonyRenderWithParametersMatcher
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
        $renderTemplatesWithParameters = $this->symfonyRenderWithParametersMatcher->matchSymfonyRender($node, $scope);

        $missingVariableNames = [];
        foreach ($renderTemplatesWithParameters as $renderTemplateWithParameters) {
            $missingVariableNames = array_merge($missingVariableNames, $this->missingTwigTemplateRenderVariableResolver->resolveFromTemplateAndMethodCall(
                $renderTemplateWithParameters,
                $scope
            ));
        }

        if ($missingVariableNames === []) {
            return [];
        }

        $errorMessages = [];
        foreach ($missingVariableNames as $missingVariableName) {
            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $missingVariableName);
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'non_existing_variable' => 'value'
        ]);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'existing_variable' => 'value'
        ]);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
