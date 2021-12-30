<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\LattePHPStanCompiler\TemplateFileVarTypeDocBlocksDecorator;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\PHPStanLatteRules\Contract\LatteTemplateHolderInterface;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TemplatePHPStanCompiler\ErrorSkipper;
use Symplify\TemplatePHPStanCompiler\PHPStan\FileAnalyserProvider;
use Symplify\TemplatePHPStanCompiler\Reporting\TemplateErrorsFactory;
use Symplify\TemplatePHPStanCompiler\Rules\TemplateRulesRegistry;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
use Throwable;

/**
 * @see \Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\LatteCompleteCheckRuleTest
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
    ];

    private TemplateRulesRegistry $templateRulesRegistry;

    /**
     * @param Rule[] $rules
     * @param LatteTemplateHolderInterface[] $latteTemplateHolders
     */
    public function __construct(
        array $rules,
        private array $latteTemplateHolders,
        private SmartFileSystem $smartFileSystem,
        private TemplateFileVarTypeDocBlocksDecorator $templateFileVarTypeDocBlocksDecorator,
        private ErrorSkipper $errorSkipper,
        private TemplateErrorsFactory $templateErrorsFactory,
        private FileAnalyserProvider $fileAnalyserProvider,
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
        return [Node::class];
    }

    /**
     * @return RuleError[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errors = [];
        foreach ($this->latteTemplateHolders as $latteTemplateHolder) {
            if (! $latteTemplateHolder->check($node, $scope)) {
                continue;
            }

            $renderTemplatesWithParameters = $latteTemplateHolder->findRenderTemplateWithParameters($node, $scope);
            $componentNamesAndTypes = $latteTemplateHolder->findComponentNamesAndTypes($node, $scope);

            foreach ($renderTemplatesWithParameters as $renderTemplateWithParameter) {
                $currentErrors = $this->processTemplateFilePath(
                    $renderTemplateWithParameter,
                    $scope,
                    $componentNamesAndTypes,
                    $node->getLine()    // TODO add line to RenderTemplateWithParameters
                );

                $errors = array_merge($errors, $currentErrors);
            }
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
        RenderTemplateWithParameters $renderTemplateWithParameters,
        Scope $scope,
        array $componentNamesAndTypes,
        int $phpLine
    ): array {
        $templateFilePath = $renderTemplateWithParameters->getTemplateFilePath();
        $array = $renderTemplateWithParameters->getParametersArray();

        try {
            $phpFileContentsWithLineMap = $this->templateFileVarTypeDocBlocksDecorator->decorate(
                $templateFilePath,
                $array,
                $scope,
                $componentNamesAndTypes
            );
        } catch (Throwable) {
            // missing include/layout template or something else went wrong → we cannot analyse template here
            $errorMessage = sprintf('Template file "%s" does not exist', $templateFilePath);
            $ruleError = RuleErrorBuilder::message($errorMessage)->build();
            return [$ruleError];
        }

        $serializedRenderTemplateWithParameters = serialize($renderTemplateWithParameters);
        $tmpFilePath = sys_get_temp_dir() . '/' . md5($serializedRenderTemplateWithParameters) . '-latte-compiled.php';
        $phpFileContents = $phpFileContentsWithLineMap->getPhpFileContents();
        $this->smartFileSystem->dumpFile($tmpFilePath, $phpFileContents);

        // 5. fix missing parent nodes by using RichParser
        $fileAnalyser = $this->fileAnalyserProvider->provide();

        // to include generated class
        $fileAnalyserResult = $fileAnalyser->analyseFile($tmpFilePath, [$tmpFilePath], $this->templateRulesRegistry, null);

        // remove errors related to just created class, that cannot be autoloaded
        $errors = $this->errorSkipper->skipErrors($fileAnalyserResult->getErrors(), self::USELESS_ERRORS_IGNORES);

        return $this->templateErrorsFactory->createErrors(
            $errors,
            $scope->getFile(),
            $templateFilePath,
            $phpFileContentsWithLineMap,
            $phpLine
        );
    }
}
