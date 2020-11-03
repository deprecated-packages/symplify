<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use ReflectionClass;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoDefaultExceptionRule\NoDefaultExceptionRuleTest
 */
final class NoDefaultExceptionRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use custom exceptions instead of native "%s"';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Throw_::class];
    }

    /**
     * @param Throw_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof New_) {
            return [];
        }

        /** @var New_ $new */
        $new = $node->expr;
        if (! $new->class instanceof Name) {
            return [];
        }

        $exceptionClass = (string) $new->class;
        if (! is_a($exceptionClass, Exception::class, true)) {
            return [];
        }

        $reflectionClass = new ReflectionClass($exceptionClass);
        if (! $reflectionClass->isInternal()) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $exceptionClass)];
    }
}
