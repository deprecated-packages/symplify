<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;

final class TemplateRenderAnalyzer
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NetteTypeAnalyzer
     */
    private $controlTypeAnalyzer;

    public function __construct(SimpleNameResolver $simpleNameResolver, NetteTypeAnalyzer $controlTypeAnalyzer)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->controlTypeAnalyzer = $controlTypeAnalyzer;
    }

    public function isTemplateRenderMethodCall(MethodCall $methodCall, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isName($methodCall->name, 'render')) {
            return false;
        }

        return $this->controlTypeAnalyzer->isTemplateType($methodCall->var, $scope);
    }
}
