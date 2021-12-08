<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\AutowiredMethodPropertyAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\ValidNetteInjectRule\ValidNetteInjectRuleTest
 */
final class ValidNetteInjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property with @inject annotation or #[Nette\DI\Attributes\Inject] attribute must be public';

    public function __construct(
        private AutowiredMethodPropertyAnalyzer $autowiredMethodAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class];
    }

    /**
     * @param Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->autowiredMethodAnalyzer->detect($node)) {
            return [];
        }

        if ($node->isPublic()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\DI\Attributes\Inject;

class SomeClass
{
    #[Inject]
    private $someDependency;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\DI\Attributes\Inject;

class SomeClass
{
    #[Inject]
    public $someDependency;
}
CODE_SAMPLE
            ),
        ]);
    }
}
