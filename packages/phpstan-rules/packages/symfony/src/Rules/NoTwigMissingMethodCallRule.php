<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule;
use Symplify\PHPStanRules\Rules\NoDynamicNameRule;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyRenderWithParametersMatcher;
use Symplify\PHPStanRules\Symfony\Twig\TwigMissingMethodCallAnalyzer;
use Symplify\PHPStanRules\Symfony\Twig\TwigNodeParser;
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndMissingMethodName;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @todo generic rule potential
 * @see \Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\NoTwigMissingMethodCallRuleTest
 */
final class NoTwigMissingMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "%s" of type "%s" does not have "%s()" method';

    /**
     * @todo possibly extract to own service provider
     * @var array<class-string<DocumentedRuleInterface>>
     */
    private const EXCLUDED_RULES = [ForbiddenFuncCallRule::class, NoDynamicNameRule::class];

    /**
     * @param Rule[] $rules
     */
    public function __construct(
        array $rules,
        private TwigNodeParser $twigNodeParser,
        private TwigMissingMethodCallAnalyzer $twigMissingMethodCallAnalyzer,
        private SymfonyRenderWithParametersMatcher $symfonyRenderWithParametersMatcher,
    ) {
        // limit rule here, as template class can contain lot of allowed Latte magic
        // get missing method + missing property etc. rule
        $activeRules = $this->filterActiveRules($rules);
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
        $renderTemplateWithParameters = $this->symfonyRenderWithParametersMatcher->matchTwigRender($node, $scope);
        if (! $renderTemplateWithParameters instanceof RenderTemplateWithParameters) {
            return [];
        }

        $phpContent = $this->twigNodeParser->compileFilePath($renderTemplateWithParameters->getTemplateFilePath());
        dump($phpContent);

        dump($renderTemplateWithParameters->getTemplateFilePath());

        // create PHP version of Twig template

        // dump($this->fileAnalyser->analyseFile());

        die;
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
            'some' => new SomeObject()
        ]);
    }
}

// some_file.twig
{{ some.non_existing_method }}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'some' => new SomeObject()
        ]);
    }
}

// some_file.twig
{{ some.existing_method }}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param VariableAndMissingMethodName[] $variableNamesToMissingMethodNames
     * @return string[]
     */
    private function createErrorMessages(array $variableNamesToMissingMethodNames): array
    {
        $errorMessages = [];

        foreach ($variableNamesToMissingMethodNames as $variableAndMissingMethodName) {
            $errorMessages[] = sprintf(
                self::ERROR_MESSAGE,
                $variableAndMissingMethodName->getVariableName(),
                $variableAndMissingMethodName->getVariableTypeClassName(),
                $variableAndMissingMethodName->getMethodName()
            );
        }

        return $errorMessages;
    }

    /**
     * @param Rule[] $rules
     * @return Rule[]
     */
    private function filterActiveRules(array $rules): array
    {
        $activeRules = [];

        foreach ($rules as $rule) {
            foreach (self::EXCLUDED_RULES as $excludedRule) {
                if (is_a($rule, $excludedRule, true)) {
                    continue 2;
                }
            }

            $activeRules[] = $rule;
        }
        return $activeRules;
    }
}
