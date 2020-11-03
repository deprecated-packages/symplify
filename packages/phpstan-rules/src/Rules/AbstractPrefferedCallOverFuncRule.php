<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

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
     * @param string[] $call
     */
    protected function isFuncCallToCallMatch(FuncCall $funcCall, Scope $scope, string $functionName, array $call): bool
    {
        if (! $this->nodeNameResolver->isName($funcCall->name, $functionName)) {
            return false;
        }

        return ! $this->isInDesiredMethod($scope, $call[0], $call[1]);
    }

    private function isInDesiredMethod(Scope $scope, string $class, string $method): bool
    {
        $function = $scope->getFunction();
        if (! $function instanceof MethodReflection) {
            return false;
        }

        if ($function->getName() !== $method) {
            return false;
        }

        $classReflection = $function->getDeclaringClass();
        return $classReflection->getName() === $class;
    }
}
