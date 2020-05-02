<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoDebugFuncCallRule\NoDebugFuncCallRuleTest
 */
final class NoDebugFuncCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Debug function "%s()" cannot be left in the code';

    /**
     * @var string[]
     */
    private const FORBIDDEN_DEBUG_FUNCTIONS = ['d', 'dd', 'dump', 'var_dump'];

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $funcName = $node->name->toString();

        if (! in_array($funcName, self::FORBIDDEN_DEBUG_FUNCTIONS, true)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $funcName)];
    }
}
