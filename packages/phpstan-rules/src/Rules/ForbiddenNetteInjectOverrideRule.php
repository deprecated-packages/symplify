<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Nette\NetteInjectAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule\ForbiddenNetteInjectOverrideRuleTest
 */
final class ForbiddenNetteInjectOverrideRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign to already injected property is not allowed';

    /**
     * @var NetteInjectAnalyzer
     */
    private $netteInjectAnalyzer;

    public function __construct(NetteInjectAnalyzer $netteInjectAnalyzer)
    {
        $this->netteInjectAnalyzer = $netteInjectAnalyzer;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof PropertyFetch) {
            return [];
        }

        $propertyFetch = $node->var;
        if (! $this->netteInjectAnalyzer->isParentInjectPropertyFetch($propertyFetch, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
abstract class AbstractParent
{
    /**
     * @inject
     * @var SomeType
     */
    protected $someType;
}

final class SomeChild extends AbstractParent
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
abstract class AbstractParent
{
    /**
     * @inject
     * @var SomeType
     */
    protected $someType;
}

final class SomeChild extends AbstractParent
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
