<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule\ForbiddenTraitUseRuleTest
 */
final class ForbiddenTraitUseRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait "%s" cannot be used in the code';

    /**
     * @param string[] $forbiddenTraits
     */
    public function __construct(
        private array $forbiddenTraits,
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [TraitUse::class];
    }

    /**
     * @param TraitUse $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->traits as $traitName) {
            $traitUseName = $this->simpleNameResolver->getName($traitName);
            if (! in_array($traitUseName, $this->forbiddenTraits, true)) {
                continue;
            }

            $ruleErrors[] = sprintf(self::ERROR_MESSAGE, $traitUseName);
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use Nette\SmartObject;

class SomeClass
{
    use SmartObject;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
}
CODE_SAMPLE
            ,
                [
                    'forbiddenTraits' => ['Nette\SmartObject'],
                ]
            ),
        ]);
    }
}
