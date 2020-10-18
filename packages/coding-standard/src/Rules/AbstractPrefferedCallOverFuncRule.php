<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

abstract class AbstractPrefferedCallOverFuncRule extends AbstractSymplifyRule
{
    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
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
     * @param array<string, string[]> $funcCallToPrefferedCalls
     * @return string[]
     */
    protected function getErrorMessageParameters(Node $node, Scope $scope, array $funcCallToPrefferedCalls): array
    {
        foreach ($funcCallToPrefferedCalls as $funcCall => $call) {
            if (! $this->nodeNameResolver->isName($node->name, $funcCall)) {
                return [];
            }

            if ($this->isInDesiredMethod($scope, $call)) {
                return [];
            }

            return [$call[0], $call[1], $funcCall];
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
