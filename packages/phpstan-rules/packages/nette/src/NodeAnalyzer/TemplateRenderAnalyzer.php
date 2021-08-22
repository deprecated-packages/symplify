<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\Astral\Naming\SimpleNameResolver;

final class TemplateRenderAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer
    ) {
    }

    public function isNetteTemplateRenderMethodCall(MethodCall $methodCall, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isNames($methodCall->name, ['render', 'action'])) {
            return false;
        }

        return $this->netteTypeAnalyzer->isTemplateType($methodCall->var, $scope);
    }

    public function isSymfonyTemplateRenderMethodCall(MethodCall $methodCall, Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if (! $classReflection->isSubclassOf(AbstractController::class)) {
            return false;
        }

        $methodCallReturnType = $scope->getType($methodCall);
        if (! $methodCallReturnType instanceof ObjectType) {
            return false;
        }

        return $methodCallReturnType->isInstanceOf(Response::class)->yes();
    }
}
