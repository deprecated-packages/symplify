<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\NodeFactory;

use PhpParser\Builder\Method;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Symplify\TemplateChecker\PhpParser\ReflectionMethodToClassMethodParser;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;

final class InvokeClassMethodFactory
{
    /**
     * @var ReflectionMethodToClassMethodParser
     */
    private $reflectionMethodToClassMethodParser;

    public function __construct(ReflectionMethodToClassMethodParser $reflectionMethodToClassMethodParser)
    {
        $this->reflectionMethodToClassMethodParser = $reflectionMethodToClassMethodParser;
    }

    public function create(ClassMethodName $classMethodName): ClassMethod
    {
        $method = new Method('__invoke');
        $method->makePublic();

        $staticCall = $this->createStaticCall($classMethodName);
        $return = new Return_($staticCall);

        $method->addStmt($return);

        $this->decorateWithOriginalParamsAndReturn($classMethodName, $method);

        return $method->getNode();
    }

    private function decorateWithOriginalParamsAndReturn(ClassMethodName $classMethodName, Method $method): void
    {
        $originalClassMethod = $this->reflectionMethodToClassMethodParser->parse(
            $classMethodName->getReflectionMethod()
        );

        if ($originalClassMethod->getParams() !== []) {
            $method->addParams($originalClassMethod->getParams());
        }

        $originalReturnType = $originalClassMethod->getReturnType();
        if ($originalReturnType !== null) {
            $method->setReturnType($originalReturnType);
        }
    }

    private function createStaticCall(ClassMethodName $classMethodName): StaticCall
    {
        $fullyQualified = new FullyQualified($classMethodName->getClass());

        $originalClassMethod = $this->reflectionMethodToClassMethodParser->parse(
            $classMethodName->getReflectionMethod()
        );
        $args = $this->convertParamsToArgs($originalClassMethod->params);

        return new StaticCall($fullyQualified, $classMethodName->getMethod(), $args);
    }

    /**
     * @param Param[] $params
     * @return Arg[]
     */
    private function convertParamsToArgs(array $params): array
    {
        $args = [];
        foreach ($params as $param) {
            $args[] = new Arg($param->var);
        }

        return $args;
    }
}
