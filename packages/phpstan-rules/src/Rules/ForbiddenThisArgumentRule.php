<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\ForbiddenThisArgumentRule
 */
final class ForbiddenThisArgumentRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '$this as argument is not allowed';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Arg::class];
    }

    /**
     * @param Arg $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->value instanceof Variable) {
            return [];
        }

        if ($node->value->name !== 'this') {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$this->someService->process($this, ...);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->someService->process($value, ...);
CODE_SAMPLE
            ),
        ]);
    }
}
