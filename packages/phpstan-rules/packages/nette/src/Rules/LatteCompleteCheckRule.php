<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
<<<<<<< HEAD
use Symplify\PHPStanRules\ErrorSkipper;
=======
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\PhpFileContentsWithLineMap;
use Symplify\PHPStanRules\Nette\FileSystem\PHPLatteFileDumper;
>>>>>>> bump php-parser to 4.13
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\Nette\PHPStan\LattePHPStanRulesRegistryAndIgnoredErrorsFilter;
use Symplify\PHPStanRules\Nette\TemplateFileVarTypeDocBlocksDecorator;
use Symplify\PHPStanRules\Nette\TypeAnalyzer\ComponentMapResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
<<<<<<< HEAD
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;
use Symplify\PHPStanRules\Templates\RenderTemplateWithParametersMatcher;
use Symplify\PHPStanRules\Templates\TemplateErrorsFactory;
use Symplify\PHPStanRules\Templates\TemplateRulesRegistry;
use Symplify\PHPStanRules\ValueObject\ComponentNameAndType;
=======
>>>>>>> bump php-parser to 4.13
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\LatteCompleteCheckRuleTest
 *
 * @inspired at https://github.com/efabrica-team/phpstan-latte/blob/main/src/Rule/ControlLatteRule.php#L56
 */
final class LatteCompleteCheckRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Complete analysis of PHP code generated from Latte template';

<<<<<<< HEAD
    /**
     * @var string[]
     */
    private const USELESS_ERRORS_IGNORES = [
        // nette
        '#DummyTemplateClass#',
        '#Method Nette\\\\Application\\\\UI\\\\Renderable::redrawControl\(\) invoked with#',
        '#Ternary operator condition is always (.*?)#',
        '#Access to an undefined property Latte\\\\Runtime\\\\FilterExecutor::#',
        '#Anonymous function should have native return typehint "void"#',
    ];

    private TemplateRulesRegistry $templateRulesRegistry;
=======
    private Registry $registry;
>>>>>>> bump php-parser to 4.13

    /**
     * @param Rule[] $rules
     */
    public function __construct(
        array $rules,
        private FileAnalyser $fileAnalyser,
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
<<<<<<< HEAD
        private RenderTemplateWithParametersMatcher $renderTemplateWithParametersMatcher,
        private SmartFileSystem $smartFileSystem,
        private TemplateFileVarTypeDocBlocksDecorator $templateFileVarTypeDocBlocksDecorator,
        private ErrorSkipper $errorSkipper,
        private TemplateErrorsFactory $templateErrorsFactory,
        private ComponentMapResolver $componentMapResolver,
    ) {
        // limit rule here, as template class can contain lot of allowed Latte magic
        // get missing method + missing property etc. rule
        $this->templateRulesRegistry = new TemplateRulesRegistry($rules);
=======
        private PathResolver $pathResolver,
        private TemplateFileVarTypeDocBlocksDecorator $templateFileVarTypeDocBlocksDecorator,
        private PHPLatteFileDumper $phpLatteFileDumper,
        private LattePHPStanRulesRegistryAndIgnoredErrorsFilter $lattePHPStanRulesRegistryAndIgnoredErrorsFilter
    ) {
        // limit rule here, as template class can contain lot of allowed Latte magic
        // get missing method + missing property etc. rule
        $activeRules = $this->lattePHPStanRulesRegistryAndIgnoredErrorsFilter->filterActiveRules($rules);

        // HACK for prevent circular reference...
        $this->registry = new Registry($activeRules);
>>>>>>> bump php-parser to 4.13
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
     * @return RuleError[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->templateRenderAnalyzer->isNetteTemplateRenderMethodCall($node, $scope)) {
            return [];
        }

        $renderTemplateWithParameters = $this->renderTemplateWithParametersMatcher->match($node, $scope, 'latte');
        if (! $renderTemplateWithParameters instanceof RenderTemplateWithParameters) {
            return [];
        }

<<<<<<< HEAD
        $componentNamesAndTypes = $this->componentMapResolver->resolveFromMethodCall($node, $scope);

        $errors = [];
        foreach ($renderTemplateWithParameters->getTemplateFilePaths() as $resolvedTemplateFilePath) {
            $currentErrors = $this->processTemplateFilePath(
=======
        $firstArg = $node->args[0];
        if (! $firstArg instanceof Arg) {
            return [];
        }

        $firstArgValue = $firstArg->value;

        $resolvedTemplateFilePath = $this->pathResolver->resolveExistingFilePath($firstArgValue, $scope);
        if ($resolvedTemplateFilePath === null) {
            return [];
        }

        $secondArgOrVariadicPlaceholder = $node->args[1];
        if (! $secondArgOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        $secondArgValue = $secondArgOrVariadicPlaceholder->value;
        if (! $secondArgValue instanceof Array_) {
            return [];
        }

        try {
            $phpFileContentsWithLineMap = $this->templateFileVarTypeDocBlocksDecorator->decorate(
>>>>>>> bump php-parser to 4.13
                $resolvedTemplateFilePath,
                $renderTemplateWithParameters->getParametersArray(),
                $scope,
                $componentNamesAndTypes
            );
<<<<<<< HEAD

            $errors = array_merge($errors, $currentErrors);
        }
=======
        } catch (Throwable) {
            // missing include/layout template or something else went wrong → we cannot analyse template here
            return [];
        }

        $dumperPhpFilePath = $this->phpLatteFileDumper->dump($phpFileContentsWithLineMap, $scope);

        // to include generated class
        $fileAnalyserResult = $this->fileAnalyser->analyseFile($dumperPhpFilePath, [], $this->registry, null);

        // remove errors related to just created class, that cannot be autoloaded
        $errors = $this->lattePHPStanRulesRegistryAndIgnoredErrorsFilter->filterErrors(
            $fileAnalyserResult->getErrors()
        );
>>>>>>> bump php-parser to 4.13

        return $errors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

class SomeClass extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_control.latte', [
            'some_type' => new SomeType
        ]);
    }
}

// some_control.latte
{$some_type->missingMethod()}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

class SomeClass extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_control.latte', [
            'some_type' => new SomeType
        ]);
    }
}


// some_control.latte
{$some_type->existingMethod()}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param ComponentNameAndType[] $componentNamesAndTypes
     * @return RuleError[]
     */
    private function processTemplateFilePath(
        string $templateFilePath,
        Array_ $array,
        Scope $scope,
        array $componentNamesAndTypes
    ): array {
        try {
            $phpFileContentsWithLineMap = $this->templateFileVarTypeDocBlocksDecorator->decorate(
                $templateFilePath,
                $array,
                $scope,
                $componentNamesAndTypes
            );
        } catch (Throwable) {
            // missing include/layout template or something else went wrong → we cannot analyse template here
            return [];
        }

<<<<<<< HEAD
        $tmpFilePath = sys_get_temp_dir() . '/' . md5($scope->getFile()) . '-latte-compiled.php';
        $phpFileContents = $phpFileContentsWithLineMap->getPhpFileContents();

        $this->smartFileSystem->dumpFile($tmpFilePath, $phpFileContents);

        // to include generated class
        $fileAnalyserResult = $this->fileAnalyser->analyseFile($tmpFilePath, [], $this->templateRulesRegistry, null);

        // remove errors related to just created class, that cannot be autoloaded
        $errors = $this->errorSkipper->skipErrors($fileAnalyserResult->getErrors(), self::USELESS_ERRORS_IGNORES);

        return $this->templateErrorsFactory->createErrors($errors, $templateFilePath, $phpFileContentsWithLineMap);
=======
        return $ruleErrors;
>>>>>>> bump php-parser to 4.13
    }
}
