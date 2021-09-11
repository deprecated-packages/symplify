<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\FileAnalyserResult;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Methods\CallMethodsRule;
use PHPStan\Rules\Registry;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanRules\Nette\TemplateFileVarTypeDocBlocksDecorator;
use Symplify\PHPStanRules\Nette\ValueObject\PhpFileContentsWithLineMap;
use Symplify\PHPStanRules\NodeAnalyzer\PathResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\NoLatteMissingMethodCallRule\NoLatteMissingMethodCallRuleTest
 */
final class NoLatteMissingMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "%s" of type "%s" does not have "%s()" method';

    private Registry $registry;

    /**
     * @inspired at https://github.com/efabrica-team/phpstan-latte/blob/main/src/Rule/ControlLatteRule.php#L56
     *
     * @param Rule[] $rules
     */
    public function __construct(
        array $rules,
        private FileAnalyser $fileAnalyser,
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private PathResolver $pathResolver,
        private SmartFileSystem $smartFileSystem,
        private TemplateFileVarTypeDocBlocksDecorator $templateFileVarTypeDocBlocksDecorator
    ) {
        // limit rule here, as template class can contain lot of allowed Latte magic
        // get missing method + missing property etc. rule
        $activeRules = [];
        foreach ($rules as $rule) {
            if (! $rule instanceof CallMethodsRule) {
                continue;
            }

            $activeRules[] = $rule;
        }

        // HACK for prevent circular reference...
        $this->registry = new Registry($activeRules);
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

        // must be template path + variables
        if (count($node->args) !== 2) {
            return [];
        }

        $firstArgValue = $node->args[0]->value;

        $resolvedTemplateFilePath = $this->pathResolver->resolveExistingFilePath($firstArgValue, $scope);
        if ($resolvedTemplateFilePath === null) {
            return [];
        }

        // use try/catch approach
//        // nothing we can do - nested templates - @todo possibly improve for included/excluded files with known paths
//        if ($this->latteTemplateMacroAnalyzer->hasMacros($resolvedTemplateFilePath, ['include', 'extends'])) {
//            return [];
//        }

        $secondArgValue = $node->args[1]->value;
        if (! $secondArgValue instanceof Array_) {
            return [];
        }

        $phpFileContentsWithLineMap = $this->templateFileVarTypeDocBlocksDecorator->decorate(
            $resolvedTemplateFilePath,
            $secondArgValue,
            $scope
        );

        $phpContent = $phpFileContentsWithLineMap->getPhpFileContents();

        $tmpFilePath = sys_get_temp_dir() . '/' . md5($scope->getFile()) . '-latte-compiled.php';

        $this->smartFileSystem->dumpFile($tmpFilePath, $phpContent);
        $this->smartFileSystem->dumpFile(getcwd() . '/file_dump.php', $phpContent);

        $result = $this->fileAnalyser->analyseFile($tmpFilePath, [[
            $tmpFilePath => true,
        ]], $this->registry, null);

        return $this->createErrors($result, $resolvedTemplateFilePath, $phpFileContentsWithLineMap);
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
        // @todo
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

class SomeClass extends Control
{
    public function render()
    {
        // @todo
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return RuleError[]
     */
    private function createErrors(
        FileAnalyserResult $fileAnalyserResult,
        string $resolvedTemplateFilePath,
        PhpFileContentsWithLineMap $phpFileContentsWithLineMap
    ): array {
        $ruleErrors = [];

        $phpToTemplateLines = $phpFileContentsWithLineMap->getPhpToTemplateLines();

        foreach ($fileAnalyserResult->getErrors() as $error) {
            // correct error PHP line number to Latte line number
            $errorLine = (int) $error->getLine();
            $errorLine = $phpToTemplateLines[$errorLine] ?? $errorLine;

            $ruleErrors[] = RuleErrorBuilder::message($error->getMessage())
                ->file($resolvedTemplateFilePath)
                ->line($errorLine)
                ->build();
        }

        return $ruleErrors;
    }
}
