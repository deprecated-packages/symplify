<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory\Service;

use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;

final class SingleServicePhpNodeFactory
{
    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;

    public function __construct(ArgsNodeFactory $argsNodeFactory)
    {
        $this->argsNodeFactory = $argsNodeFactory;
    }

    /**
     * @see https://symfony.com/doc/current/service_container/injection_types.html
     */
    public function createProperties(MethodCall $methodCall, array $properties): MethodCall
    {
        foreach ($properties as $name => $value) {
            $args = $this->argsNodeFactory->createFromValues([$name, $value]);
            $methodCall = new MethodCall($methodCall, 'property', $args);
        }

        return $methodCall;
    }

    /**
     * @see https://symfony.com/doc/current/service_container/injection_types.html
     */
    public function createCalls(MethodCall $methodCall, array $calls): MethodCall
    {
        foreach ($calls as $call) {
            // @todo can be more items
            $args = [];

            $methodName = $this->resolveCallMethod($call);
            $args[] = new Arg($methodName);

            $argumentsExpr = $this->resolveCallArguments($call);
            $args[] = new Arg($argumentsExpr);

            $returnCloneExpr = $this->resolveCallReturnClone($call);
            if ($returnCloneExpr !== null) {
                $args[] = new Arg($returnCloneExpr);
            }

            $currentArray = current($call);
            if ($currentArray instanceof TaggedValue) {
                $args[] = new Arg(BuilderHelpers::normalizeValue(true));
            }

            $methodCall = new MethodCall($methodCall, 'call', $args);
        }

        return $methodCall;
    }

    private function resolveCallMethod($call): String_
    {
        return new String_($call[0] ?? $call['method'] ?? key($call));
    }

    private function resolveCallArguments($call): Expr
    {
        $arguments = $call[1] ?? $call['arguments'] ?? current($call);
        return $this->argsNodeFactory->resolveExpr($arguments);
    }

    private function resolveCallReturnClone(array $call): ?Expr
    {
        if (isset($call[2]) || isset($call['returns_clone'])) {
            $returnsCloneValue = $call[2] ?? $call['returns_clone'];
            return BuilderHelpers::normalizeValue($returnsCloneValue);
        }

        return null;
    }
}
