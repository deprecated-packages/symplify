<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRuleTest
 */
final class PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule extends AbstractCheckFunctionCallRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s::%s()" method call over "%s()" func call';
}
