<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;
use Symplify\PHPStanTwigRules\Templates\RenderTemplateWithParametersMatcher;
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

        return $this->renderTemplateWithParametersMatcher->match($methodCall, $scope, 'twig');
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

        return $this->renderTemplateWithParametersMatcher->match($methodCall, $scope, 'twig');
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
}
