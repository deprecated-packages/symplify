<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PrefferedStaticCallOverFuncCallRule\PrefferedStaticCallOverFuncCallRuleTest
 */
final class PrefferedStaticCallOverFuncCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s::%s()" static call over "%s()" func call';

    /**
     * @var array<string, string[]>
     */
    private $funcCallToPrefferedStaticCalls = [];

    /**
     * @param array<string, string[]> $funcCallToPrefferedStaticCalls
     */
    public function __construct(array $funcCallToPrefferedStaticCalls = [])
    {
        $this->funcCallToPrefferedStaticCalls = $funcCallToPrefferedStaticCalls;
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

        $currentFuncName = $scope->resolveName($node->name);
        foreach ($this->funcCallToPrefferedStaticCalls as $funcCall => $staticCall) {
            if ($funcCall !== $currentFuncName) {
                continue;
            }

            if ($this->isInDesiredMethod($scope, $staticCall)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $staticCall[0], $staticCall[1], $currentFuncName);
            return [$errorMessage];
        }

        return [];
    }

    /**
     * @param string[] $staticCall
     */
    private function isInDesiredMethod(Scope $scope, array $staticCall): bool
    {
        $methodReflection = $scope->getFunction();
        if (! $methodReflection instanceof MethodReflection) {
            return false;
        }

        if ($methodReflection->getName() !== $staticCall[1]) {
            return false;
        }

        $declaringClass = $methodReflection->getDeclaringClass();
        return $declaringClass->getName() === $staticCall[0];
    }
}
