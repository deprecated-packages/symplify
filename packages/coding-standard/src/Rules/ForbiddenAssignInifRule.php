<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenAssignInifRule\ForbiddenAssignInifRuleTest
 */
final class ForbiddenAssignInifRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assignment inside if is not allowed. Use before if instead.';

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
        if (! $this->isInsideIf($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isInsideIf(Assign $assign): bool
    {
        $if = $assign->getAttribute(PHPStanAttributeKey::PARENT);
        while ($if) {
            if ($if instanceof If_) {
                break;
            }

            $if = $if->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return $if instanceof If_;
    }
}
