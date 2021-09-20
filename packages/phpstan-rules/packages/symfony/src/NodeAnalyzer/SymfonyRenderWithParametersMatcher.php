<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

<<<<<<< HEAD
=======
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
>>>>>>> bump php-parser to 4.13
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;
use Symplify\PHPStanRules\Templates\RenderTemplateWithParametersMatcher;
use Twig\Environment;

final class SymfonyRenderWithParametersMatcher
{
    /**
     * @var string
     */
    private const RENDER = 'render';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private RenderTemplateWithParametersMatcher $renderTemplateWithParametersMatcher,
    ) {
    }

    public function matchSymfonyRender(MethodCall $methodCall, Scope $scope): RenderTemplateWithParameters|null
    {
        if (! $this->simpleNameResolver->isNames($methodCall->name, [self::RENDER, 'renderView'])) {
            return null;
        }

        $methodCallReturnType = $scope->getType($methodCall);
        if (! $methodCallReturnType instanceof ObjectType) {
            return null;
        }

        if (! $methodCallReturnType->isInstanceOf(Response::class)->yes()) {
            return null;
        }

<<<<<<< HEAD
        return $this->renderTemplateWithParametersMatcher->match($methodCall, $scope, 'twig');
=======
        return $this->matchRenderTemplateWithParmatersOnArgs($methodCall, $scope);
>>>>>>> bump php-parser to 4.13
    }

    public function matchTwigRender(MethodCall $methodCall, Scope $scope): RenderTemplateWithParameters|null
    {
        $callerType = $scope->getType($methodCall->var);
        if ($callerType instanceof ThisType) {
            $callerType = new ObjectType($callerType->getClassName());
        }

        if (! $callerType instanceof ObjectType) {
            return null;
        }

        if (! $this->isTwigCallerType($callerType, $methodCall)) {
            return null;
        }

<<<<<<< HEAD
        return $this->renderTemplateWithParametersMatcher->match($methodCall, $scope, 'twig');
=======
        return $this->matchRenderTemplateWithParmatersOnArgs($methodCall, $scope);
    }

    private function resolveParameterArray(MethodCall $methodCall): Array_
    {
        if (! isset($methodCall->args[1])) {
            return new Array_();
        }

        $secondArgOrVariadicPlaceholder = $methodCall->args[1];
        if (! $secondArgOrVariadicPlaceholder instanceof Arg) {
            return new Array_();
        }

        $secondArgValue = $secondArgOrVariadicPlaceholder->value;
        if (! $secondArgValue instanceof Array_) {
            return new Array_();
        }

        return $secondArgValue;
>>>>>>> bump php-parser to 4.13
    }

    private function isTwigCallerType(ObjectType $objectType, MethodCall $methodCall): bool
    {
        if ($objectType->isInstanceOf(Environment::class)->yes()) {
            return $this->simpleNameResolver->isName($methodCall->name, self::RENDER);
        }

        if ($objectType->isInstanceOf(AbstractController::class)->yes()) {
            return $this->simpleNameResolver->isNames($methodCall->name, [self::RENDER, 'renderView']);
        }

        return false;
    }
<<<<<<< HEAD
=======

    private function matchRenderTemplateWithParmatersOnArgs(
        MethodCall $methodCall,
        Scope $scope
    ): ?RenderTemplateWithParameters {
        $firstArg = $methodCall->args[0];
        if (! $firstArg instanceof Arg) {
            return null;
        }

        $resolvedTemplateFilePath = $this->pathResolver->resolveExistingFilePath($firstArg->value, $scope);
        if ($resolvedTemplateFilePath === null) {
            return null;
        }

        // we need array parameters
        $parametersArray = $this->resolveParameterArray($methodCall);

        return new RenderTemplateWithParameters($resolvedTemplateFilePath, $parametersArray);
    }
>>>>>>> bump php-parser to 4.13
}
