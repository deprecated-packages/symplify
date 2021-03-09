<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\Php\PhpMethodFromParserNodeReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class VariableAsParamAnalyser
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(PrivatesAccessor $privatesAccessor, SimpleNameResolver $simpleNameResolver)
    {
        $this->privatesAccessor = $privatesAccessor;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function isVariableFromConstructorParam(MethodReflection $methodReflection, Variable $variable): bool
    {
        if ($methodReflection->getName() !== MethodName::CONSTRUCTOR) {
            return false;
        }

        if (! $methodReflection instanceof PhpMethodFromParserNodeReflection) {
            return false;
        }

        $constructorClassMethod = $this->privatesAccessor->getPrivateProperty($methodReflection, 'functionLike');
        if (! $constructorClassMethod instanceof ClassMethod) {
            return false;
        }

        if ($variable->name instanceof Expr) {
            return false;
        }

        $variableName = $variable->name;

        foreach ($constructorClassMethod->params as $param) {
            if ($this->simpleNameResolver->isName($param, $variableName)) {
                return true;
            }
        }

        return false;
    }
}
