<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Nette\NetteInjectAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\ForbiddenNetteInjectOverrideRuleTest
 */
final class ForbiddenNetteInjectOverrideRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign to already injected property is not allowed';

    public function __construct(
        private NetteInjectAnalyzer $netteInjectAnalyzer
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
use Nette\DI\Attributes\Inject;

abstract class AbstractParent
{
    /**
     * @var SomeType
     */
    #[Inject]
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
use Nette\DI\Attributes\Inject;

abstract class AbstractParent
{
    /**
     * @var SomeType
     */
    #[Inject]
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
