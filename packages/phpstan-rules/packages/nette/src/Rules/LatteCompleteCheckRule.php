<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\LattePHPStanCompiler\TemplateFileVarTypeDocBlocksDecorator;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\PHPStanRules\ErrorSkipper;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\Nette\Templates\TemplateWithParametersMatcher;
use Symplify\PHPStanRules\Nette\TypeAnalyzer\ComponentMapResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;
use Symplify\PHPStanRules\Templates\TemplateRulesRegistry;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TemplatePHPStanCompiler\Reporting\TemplateErrorsFactory;
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

    /**
     * @param Rule[] $rules
     */
    public function __construct(
        array $rules,
        private FileAnalyser $fileAnalyser,
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private TemplateWithParametersMatcher $templateWithParametersMatcher,
        private SmartFileSystem $smartFileSystem,
        private TemplateFileVarTypeDocBlocksDecorator $templateFileVarTypeDocBlocksDecorator,
        private ErrorSkipper $errorSkipper,
        private TemplateErrorsFactory $templateErrorsFactory,
        private ComponentMapResolver $componentMapResolver,
    ) {
        // limit rule here, as template class can contain a lot of allowed Latte magic
        // get missing method + missing property etc. rule
        $this->templateRulesRegistry = new TemplateRulesRegistry($rules);
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

        $renderTemplateWithParameters = $this->templateWithParametersMatcher->match($node, $scope);
        if (! $renderTemplateWithParameters instanceof RenderTemplateWithParameters) {
            return [];
        }

        $componentNamesAndTypes = $this->componentMapResolver->resolveFromMethodCall($node, $scope);

        $errors = [];
        foreach ($renderTemplateWithParameters->getTemplateFilePaths() as $resolvedTemplateFilePath) {
            $currentErrors = $this->processTemplateFilePath(
                $resolvedTemplateFilePath,
                $renderTemplateWithParameters->getParametersArray(),
                $scope,
                $componentNamesAndTypes
            );

            $errors = array_merge($errors, $currentErrors);
        }

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
            return [RuleErrorBuilder::message('Template file ' . $templateFilePath . ' doesn\'t exist')->build()];
        }

        $tmpFilePath = sys_get_temp_dir() . '/' . md5($scope->getFile()) . '-latte-compiled.php';
        $phpFileContents = $phpFileContentsWithLineMap->getPhpFileContents();

        $this->smartFileSystem->dumpFile($tmpFilePath, $phpFileContents);

        // to include generated class
        $fileAnalyserResult = $this->fileAnalyser->analyseFile($tmpFilePath, [], $this->templateRulesRegistry, null);

        // remove errors related to just created class, that cannot be autoloaded
        $errors = $this->errorSkipper->skipErrors($fileAnalyserResult->getErrors(), self::USELESS_ERRORS_IGNORES);

        return $this->templateErrorsFactory->createErrors($errors, $templateFilePath, $phpFileContentsWithLineMap);
    }
}
