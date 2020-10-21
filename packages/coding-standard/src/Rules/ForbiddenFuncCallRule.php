<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenFuncCallRule\ForbiddenFuncCallRuleTest
 */
final class ForbiddenFuncCallRule extends AbstractSymplifyRule
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

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $funcName = $node->name->toString();
        foreach ($this->forbiddenFunctions as $forbiddenFunction) {
            $errorMessage = [sprintf(self::ERROR_MESSAGE, $forbiddenFunction)];

            if ($funcName === $forbiddenFunction) {
                return $errorMessage;
            }

            $isEndWithMask = Strings::endsWith($forbiddenFunction, '*');
            $isStartWithFunctionForbidden = Strings::startsWith(
                $funcName,
                Strings::substring($forbiddenFunction, 0, -1)
            );

            if ($isEndWithMask && $isStartWithFunctionForbidden) {
                return $errorMessage;
            }
        }

        return [];
    }
}
