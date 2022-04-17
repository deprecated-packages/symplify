<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\SuffixTraitRule\SuffixTraitRuleTest
 */
final class SuffixTraitRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait must be suffixed by "Trait" exclusively';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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
