<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Spotter;

use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://www.php.net/manual/en/control-structures.match.php
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Spotter\SwitchToMatchSpotterRule\SwitchToMatchSpotterRuleTest
 */
final class SwitchToMatchSpotterRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Switch construction can be replace with more robust match()';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Switch_::class];
    }

    /**
     * @param Switch_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->hasDefaultCase($node)) {
            return [];
        }

        if (! $this->isMatchingSwitch($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
switch ($key) {
    case 1:
        return 100;
    case 2:
        return 200;
    default:
        return 300;
};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
return match($key) {
    1 => 100,
    2 => 200,
    default => 300,
};
CODE_SAMPLE
            ),
        ]);
    }

    private function hasDefaultCase(Switch_ $switch): bool
    {
        foreach ($switch->cases as $case) {
            if ($case->cond === null) {
                return true;
            }
        }

        return false;
    }

    private function isMatchingSwitch(Switch_ $switch): bool
    {
        foreach ($switch->cases as $case) {
            if ($case->cond === null) {
                continue;
            }

            // no stmts, merged with another case
            if ($case->stmts === []) {
                continue;
            }

            // must be exact 1 stmts
            if (count($case->stmts) !== 1) {
                continue;
            }

            $onlyStmt = $case->stmts[0];
            if ($onlyStmt instanceof Return_ && $onlyStmt->expr !== null) {
                continue;
            }

            if ($onlyStmt instanceof Throw_) {
                continue;
            }

            return false;
        }

        return true;
    }
}
