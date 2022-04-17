<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\NoTemplateMagicAssignInControlRule\NoTemplateMagicAssignInControlRuleTest
 */
final class NoTemplateMagicAssignInControlRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of magic template assign use render() param and explicit variable';

    public function __construct(
        private NetteTypeAnalyzer $netteTypeAnalyzer
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->netteTypeAnalyzer->isTemplateMagicPropertyType($node->var, $scope)) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        // check only controls
        if ($classReflection->isSubclassOf(Presenter::class)) {
            return [];
        }

        if (! $classReflection->isSubclassOf(Control::class)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->value = 1000;

        $this->template->render(__DIR__ . '/some_file.latte');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'value' => 1000
        ]);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
