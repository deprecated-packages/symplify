<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoFunctionCallInMethodCallRule\NoFunctionCallInMethodCallRuleTest
 */
final class NoFunctionCallInMethodCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Separate function "%s()" in method call to standalone row to improve readability';

    /**
     * @var string[]
     */
    private const ALLOWED_FUNC_CALL_NAMES = ['getcwd', 'sys_get_temp_dir'];

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $messages = [];

        foreach ($node->args as $arg) {
            if (! $arg->value instanceof FuncCall) {
                continue;
            }

            $funcCallName = $this->resolveFuncCallName($arg);

            if (Strings::contains($funcCallName, '\\')) {
                continue;
            }

            if (in_array($funcCallName, self::ALLOWED_FUNC_CALL_NAMES, true)) {
                continue;
            }

            $messages[] = sprintf(self::ERROR_MESSAGE, $funcCallName);
        }

        return $messages;
    }

    private function resolveFuncCallName(Arg $arg): string
    {
        /** @var FuncCall $funcCall */
        $funcCall = $arg->value;
        if ($funcCall->name instanceof Expr) {
            return '*dynamic*';
        }

        return (string) $funcCall->name;
    }
}
