<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\ForbiddenAssignInLoopRuleTest
 */
final class ForbiddenAssignInLoopRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign in loop is not allowed.';

    /**
     * @return string[]
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
        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
foreach (...) {
    $value = new SmartFileInfo('a.php');
    if ($value) {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = new SmartFileInfo('a.php');
foreach (...) {
    if ($value) {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
