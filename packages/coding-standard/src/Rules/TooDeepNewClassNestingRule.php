<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\TooDeepNewClassNestingRule\TooDeepNewClassNestingRuleTest
 */
final class TooDeepNewClassNestingRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'new <class> is limited to %d, you have %d nesting.';

    /**
     * @var int
     */
    private $maxNewClassNesting;

    public function __construct(int $maxNewClassNesting = 3)
    {
        $this->maxNewClassNesting = $maxNewClassNesting;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $countNew = 1;
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($parent) {
            if ($parent instanceof New_) {
                ++$countNew;
            }

            $parent = $parent->getAttribute(PHPStanAttributeKey::PARENT);
        }

        if ($this->maxNewClassNesting >= $countNew) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $this->maxNewClassNesting, $countNew)];
    }
}
