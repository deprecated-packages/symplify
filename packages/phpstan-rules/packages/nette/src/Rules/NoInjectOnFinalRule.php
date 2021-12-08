<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\Nette\NetteInjectAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\NoInjectOnFinalRuleTest
 */
final class NoInjectOnFinalRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use constructor on final classes, instead of property injection';

    public function __construct(
        private NetteInjectAnalyzer $netteInjectAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Nette\DI\Attributes\Inject;

final class SomePresenter
{
     #[Inject]
    public $property;
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Nette\DI\Attributes\Inject;

abstract class SomePresenter
{
    #[Inject]
    public $property;
}
CODE_SAMPLE
                ), ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->netteInjectAnalyzer->isInjectProperty($node)) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isFinal()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
