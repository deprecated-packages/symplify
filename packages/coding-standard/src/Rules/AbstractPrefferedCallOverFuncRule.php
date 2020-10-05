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
     * @var string
     */
    public const PREFER_STATIC_CALL_ERROR_MESSAGE = 'Use "%s::%s()" static call over "%s()" func call';

    /**
     * @var string
     */
    public const PREFER_METHOD_CALL_ERROR_MESSAGE = 'Use "%s::%s()" method call over "%s()" func call';

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
        foreach ($this->funcCallToPrefferedStaticCalls as $funcCall => $staticCall) {
            if (! $this->nodeNameResolver->isName($node->name, $funcCall)) {
                continue;
            }

            if ($this->isInDesiredMethod($scope, $staticCall)) {
                return [];
            }

            $errorMessage = strpos(static::class, 'PrefferedStaticCallOverFuncCallRule') !== false
                ?  self::PREFER_STATIC_CALL_ERROR_MESSAGE
                : self:: PREFER_METHOD_CALL_ERROR_MESSAGE;

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
