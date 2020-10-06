<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use ReflectionClassConstant;
use ReflectionException;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

abstract class AbstractPrefferedCallOverFuncRule extends AbstractSymplifyRule
{
    /**
     * @var array<string, string[]>
     */
    private $funcCallToPrefferedCalls = [];

    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    /**
     * @param array<string, string[]> $funcCallToPrefferedCalls
     */
    public function __construct(NodeNameResolver $nodeNameResolver, array $funcCallToPrefferedCalls = [])
    {
        $this->funcCallToPrefferedCalls = $funcCallToPrefferedCalls;
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
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($this->funcCallToPrefferedCalls as $funcCall => $staticCall) {
            if (! $this->nodeNameResolver->isName($node->name, $funcCall)) {
                continue;
            }

            if ($this->isInDesiredMethod($scope, $staticCall)) {
                return [];
            }

            try {
                $constantReflex = new ReflectionClassConstant(static::class, 'ERROR_MESSAGE');
                $errorMessage = $constantReflex->getValue();
            } catch (ReflectionException $reflectionException) {
                throw new ShouldNotHappenException('const ERROR_MESSAGE must be defined with public modifier');
            }

            $errorMessage = sprintf($errorMessage, $staticCall[0], $staticCall[1], $funcCall);
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
