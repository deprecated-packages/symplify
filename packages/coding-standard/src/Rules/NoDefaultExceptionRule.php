<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use ReflectionClass;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoDefaultExceptionRule\NoDefaultExceptionRuleTest
 */
final class NoDefaultExceptionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use custom exceptions instead of native "%s"';

    public function getNodeType(): string
    {
        return Throw_::class;
    }

    /**
     * @param Throw_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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
