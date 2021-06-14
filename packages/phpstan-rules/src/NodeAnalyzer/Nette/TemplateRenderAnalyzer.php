<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;

final class TemplateRenderAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer
    ) {
    }

    public function isTemplateRenderMethodCall(MethodCall $methodCall, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isName($methodCall->name, 'render')) {
            return false;
        }

        return $this->netteTypeAnalyzer->isTemplateType($methodCall->var, $scope);
    }
}
