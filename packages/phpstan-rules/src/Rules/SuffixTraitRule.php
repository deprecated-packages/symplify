<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\SuffixTraitRule\SuffixTraitRuleTest
 */
final class SuffixTraitRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait must be suffixed by "Trait" exclusively';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class];
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $traitName = (string) $node->name;
        if (\str_ends_with($traitName, 'Trait')) {
            if ($node instanceof Trait_) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        }

        if ($node instanceof Trait_) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
trait SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
trait SomeTrait
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
