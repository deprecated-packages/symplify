<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PrefferedMethodCallOverFuncCallRule\PrefferedMethodCallOverFuncCallRuleTest
 */
final class PrefferedMethodCallOverFuncCallRule extends AbstractPrefferedCallOverFuncRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s->%s()" method call over "%s()" func call';
}
