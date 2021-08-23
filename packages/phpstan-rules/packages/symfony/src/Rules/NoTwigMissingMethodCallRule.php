<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\Symfony\Twig\TwigMissingMethodCallAnalyzer;
use Symplify\PHPStanRules\Symfony\Twig\TwigNodeParser;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndMissingMethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\NoTwigMissingMethodCallRuleTest
 */
final class NoTwigMissingMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "%s" does not have "%s()" method';

    public function __construct(
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private PathResolver $pathResolver,
        private TwigNodeParser $twigNodeParser,
        private TwigMissingMethodCallAnalyzer $twigMissingMethodCallAnalyzer
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

        $templateFilePath = $this->pathResolver->resolveExistingFilePath($firstArgValue, $scope);

        if ($templateFilePath === null) {
            return [];
        }

        $secondArgValue = $node->args[1]->value;
        if (! $secondArgValue instanceof Array_) {
            return [];
        }

        $moduleNode = $this->twigNodeParser->parseFilePath($templateFilePath);

        $variableNamesToMissingMethodNames = $this->twigMissingMethodCallAnalyzer->resolveFromArrayAndModuleNode(
            $secondArgValue,
            $scope,
            $moduleNode
        );

        return $this->createErrorMessages($variableNamesToMissingMethodNames);
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
                $variableAndMissingMethodName->getVariable(),
                $variableAndMissingMethodName->getMethodName()
            );
        }

        return $errorMessages;
    }
}
