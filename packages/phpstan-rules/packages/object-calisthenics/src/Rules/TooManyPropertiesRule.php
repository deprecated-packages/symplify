<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;

/**
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooManyPropertiesRule\TooManyPropertiesRuleTest
 */
final class TooManyPropertiesRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class has too many properties %d. Try narrowing it down under %d';

    /**
     * @var int
     */
    private $maxPropertyCount;

    public function __construct(int $maxPropertyCount)
    {
        $this->maxPropertyCount = $maxPropertyCount;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentPropertyCount = count($node->getProperties());
        if ($currentPropertyCount < $this->maxPropertyCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $currentPropertyCount, $this->maxPropertyCount);
        return [$errorMessage];
    }
}
