<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\NetteTypeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNetteArrayAccessInControlRule\NoNetteArrayAccessInControlRuleTest
 */
final class NoNetteArrayAccessInControlRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid using magical unclear array access and use explicit "$this->getComponent()" instead';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NetteTypeAnalyzer
     */
    private $netteTypeAnalyzer;

    public function __construct(SimpleNameResolver $simpleNameResolver, NetteTypeAnalyzer $netteTypeAnalyzer)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->netteTypeAnalyzer = $netteTypeAnalyzer;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->netteTypeAnalyzer->isInsideComponentContainer($scope)) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->var, 'this')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
        return $this['someControl'];
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
        return $this->getComponent('someControl');
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
