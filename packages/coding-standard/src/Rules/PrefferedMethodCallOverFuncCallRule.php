<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PrefferedMethodCallOverFuncCallRule\PrefferedMethodCallOverFuncCallRuleTest
 */
final class PrefferedMethodCallOverFuncCallRule extends AbstractPrefferedCallOverFuncRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s->%s()" method call over "%s()" func call';

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (($errorMessageParameters = parent::getErrorMessageParameters($node, $scope)) === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, ...$errorMessageParameters);
        return [$errorMessage];
    }
}
