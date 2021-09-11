<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\FileAnalyser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Registry;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Nette\Latte\LatteTemplateMacroAnalyzer;
use Symplify\PHPStanRules\Nette\Latte\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\TemplateRenderAnalyzer;
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
        private LatteTemplateMacroAnalyzer $latteTemplateMacroAnalyzer,
        private LatteToPhpCompiler $latteToPhpCompiler,
        private SmartFileSystem $smartFileSystem
    ) {
        // get missing method + missing property etc. rule
        foreach ($rules as $rule) {
            // dump(get_class($rule));
        }

//        die;

        $this->registry = new Registry($rules); // HACK for prevent circular reference...
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

        // nothing we can do - nested templates - @todo possibly improve for included/excluded files with known paths
        if ($this->latteTemplateMacroAnalyzer->hasMacros($resolvedTemplateFilePath, ['include', 'extends'])) {
            return [];
        }

        $phpContent = $this->latteToPhpCompiler->compileFilePath($resolvedTemplateFilePath);

        $tmpFilePath = sys_get_temp_dir() . '/' . md5($scope->getFile()) . '-latte-compiled.php';
        $this->smartFileSystem->dumpFile($tmpFilePath, $phpContent);

        $result = $this->fileAnalyser->analyseFile($tmpFilePath, [], $this->registry, null);

        $errors = [];
        foreach ($result->getErrors() as $error) {
            $errors[] = RuleErrorBuilder::message($error->getMessage())
                ->file($resolvedTemplateFilePath)
                ->line((int) $error->getLine())
                ->build();
        }

        return $errors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        // @todo
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
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
}
