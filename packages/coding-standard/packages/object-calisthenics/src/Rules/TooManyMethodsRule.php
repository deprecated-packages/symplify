<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooManyMethodsRule\TooManyMethodsRuleTest
 */
final class TooManyMethodsRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method has too many methods %d. Try narrowing it down under %d';

    /**
     * @var int
     */
    private $maxMethodCount;

    public function __construct(int $maxMethodCount)
    {
        $this->maxMethodCount = $maxMethodCount;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $currentMethodCount = count($node->getMethods());
        if ($currentMethodCount < $this->maxMethodCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $currentMethodCount, $this->maxMethodCount);
        return [$errorMessage];
    }
}
