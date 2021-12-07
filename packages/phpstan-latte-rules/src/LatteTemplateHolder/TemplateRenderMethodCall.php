<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\LatteTemplateHolder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanLatteRules\Contract\LatteTemplateHolderInterface;
use Symplify\PHPStanLatteRules\NodeAnalyzer\LatteTemplateWithParametersMatcher;
use Symplify\PHPStanLatteRules\NodeAnalyzer\TemplateRenderAnalyzer;
use Symplify\PHPStanLatteRules\TypeAnalyzer\ComponentMapResolver;

final class TemplateRenderMethodCall implements LatteTemplateHolderInterface
{
    public function __construct(
        private TemplateRenderAnalyzer $templateRenderAnalyzer,
        private LatteTemplateWithParametersMatcher $latteTemplateWithParametersMatcher,
        private ComponentMapResolver $componentMapResolver,
    ) {
    }

    public function check(Node $node, Scope $scope): bool
    {
        if (! $node instanceof MethodCall) {
            return false;
        }

        if (! $this->templateRenderAnalyzer->isNetteTemplateRenderMethodCall($node, $scope)) {
            return false;
        }

        return true;
    }

    /**
     * @param MethodCall $node
     */
    public function findRenderTemplateWithParameters(Node $node, Scope $scope): array
    {
        return $this->latteTemplateWithParametersMatcher->match($node, $scope);
    }

    /**
     * @param MethodCall $node
     */
    public function findComponentNamesAndTypes(Node $node, Scope $scope): array
    {
        return $this->componentMapResolver->resolveFromMethodCall($node, $scope);
    }
}
