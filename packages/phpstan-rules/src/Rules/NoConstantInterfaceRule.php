<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoConstantInterfaceRule\NoConstantInterfaceRuleTest
 */
final class NoConstantInterfaceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Reserve interface for contract only. Move constant holder to a class soon-to-be Enum';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Interface_::class];
    }

    /**
     * @param Interface_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->getConstants() === []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
interface SomeContract
{
    public const YES = 'yes';

    public const NO = 'ne';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeValues
{
    public const YES = 'yes';

    public const NO = 'ne';
}
CODE_SAMPLE
            ),
        ]);
    }
}
