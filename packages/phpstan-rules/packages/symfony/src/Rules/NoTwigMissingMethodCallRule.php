<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Registry;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\ErrorSkipper;
use Symplify\PHPStanRules\Nette\TemplateFileVarTypeDocBlocksDecorator;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyRenderWithParametersMatcher;
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;
use Symplify\PHPStanRules\TwigPHPStanPrinter\TwigToPhpCompiler;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @todo generic rule potential
 * @see \Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\NoTwigMissingMethodCallRuleTest
 */
final class NoTwigMissingMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Complete analysis of PHP code generated from Twig template';

    /**
     * List of errors, that do not bring any value.
     *
     * @var string[]
     */
    private const ERROR_IGNORES = [
        '#Method __TwigTemplate(.*?)::doDisplay\(\) throws checked exception Twig\\\\Error\\\\RuntimeError#',
        '#Call to method getSourceContext\(\) on an unknown class __TwigTemplate(.*?)#',
    ];

    private Registry $registry;

    /**
     * @param Rule[] $rules
     */
    public function __construct(
        array $rules,
        private SymfonyRenderWithParametersMatcher $symfonyRenderWithParametersMatcher,
        private TwigToPhpCompiler $twigToPhpCompiler,
        private TemplateFileVarTypeDocBlocksDecorator $templateFileVarTypeDocBlocksDecorator,
        private SmartFileSystem $smartFileSystem,
        private FileAnalyser $fileAnalyser,
        private ErrorSkipper $errorSkipper,
    ) {
        $this->registry = new Registry($rules);
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

        $variablesAndTypes = $this->templateFileVarTypeDocBlocksDecorator->resolveTwigVariablesAndTypes(
            $renderTemplateWithParameters->getParametersArray(),
            $scope
        );

        $phpFileContent = $this->twigToPhpCompiler->compileContent(
            $renderTemplateWithParameters->getTemplateFilePath(),
            $variablesAndTypes,
        );

        $tmpFilePath = sys_get_temp_dir() . '/' . md5($scope->getFile()) . '-twig-compiled.php';

        $this->smartFileSystem->dumpFile($tmpFilePath, $phpFileContent);

        // to include generated class
        $fileAnalyserResult = $this->fileAnalyser->analyseFile($tmpFilePath, [], $this->registry, null);

        // correct PHP to twig line
        // probably via data in getDebugInfo() method
        // return $this->createErrorMessages($variableNamesToMissingMethodNames);

        return $this->errorSkipper->skipErrors($fileAnalyserResult->getErrors(), self::ERROR_IGNORES);
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

//    /**
//     * @param VariableAndMissingMethodName[] $variableNamesToMissingMethodNames
//     * @return string[]
//     */
//    private function createErrorMessages(array $variableNamesToMissingMethodNames): array
//    {
//        $errorMessages = [];
//
//        foreach ($variableNamesToMissingMethodNames as $variableAndMissingMethodName) {
//            $errorMessages[] = sprintf(
//                self::ERROR_MESSAGE,
//                $variableAndMissingMethodName->getVariableName(),
//                $variableAndMissingMethodName->getVariableTypeClassName(),
//                $variableAndMissingMethodName->getMethodName()
//            );
//        }
//
//        return $errorMessages;
//    }
}
