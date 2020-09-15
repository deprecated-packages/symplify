<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenFuncCallRule\ForbiddenFuncCallRuleTest
 */
final class ForbiddenFuncCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Function "%s()" cannot be used/left in the code';

    /**
     * @var string[]
     */
    private $forbiddenFunctions = [];

    /**
     * @param string[] $forbiddenFunctions
     */
    public function __construct(array $forbiddenFunctions)
    {
        $this->forbiddenFunctions = $forbiddenFunctions;
    }

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

        if (! in_array($funcName, $this->forbiddenFunctions, true)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $funcName)];
    }
}
