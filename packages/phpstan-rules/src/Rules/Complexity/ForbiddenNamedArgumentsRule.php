<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule\ForbiddenNamedArgumentsRuleTest
 */
final class ForbiddenNamedArgumentsRule extends AbstractSymplifyRule
{
    /**
     * @todo exception for attributes!
     * @var string
     */
    public const ERROR_MESSAGE = 'Named arguments do not add any value here. Use normal arguments in the same order';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
return strlen(string: 'name');

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
return strlen('name');
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [CallLike::class];
    }

    /**
     * @param CallLike $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($node->getArgs() as $arg) {
            if ($arg->name === null) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }
}
