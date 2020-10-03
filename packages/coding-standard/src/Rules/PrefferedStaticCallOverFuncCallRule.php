<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

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
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    /**
     * @param array<string, string[]> $funcCallToPrefferedStaticCalls
     */
    public function __construct(NodeNameResolver $nodeNameResolver, array $funcCallToPrefferedStaticCalls = [])
    {
        $this->funcCallToPrefferedStaticCalls = $funcCallToPrefferedStaticCalls;
        $this->nodeNameResolver = $nodeNameResolver;
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
        foreach ($this->funcCallToPrefferedStaticCalls as $funcCall => $staticCall) {
            if (! $this->nodeNameResolver->isName($node->name, $funcCall)) {
                continue;
            }

            if ($this->isInDesiredMethod($scope, $staticCall)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $staticCall[0], $staticCall[1], $funcCall);
            return [$errorMessage];
        }

        return [];
    }

    /**
     * @param string[] $staticCall
     */
    private function isInDesiredMethod(Scope $scope, array $staticCall): bool
    {
        $function = $scope->getFunction();
        if (! $function instanceof MethodReflection) {
            return false;
        }

        if ($function->getName() !== $staticCall[1]) {
            return false;
        }

        $classReflection = $function->getDeclaringClass();
        return $classReflection->getName() === $staticCall[0];
    }
}
