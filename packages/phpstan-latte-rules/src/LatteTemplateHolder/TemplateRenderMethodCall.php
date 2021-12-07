<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\LatteTemplateHolder;

use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
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

        return $this->templateRenderAnalyzer->isNetteTemplateRenderMethodCall($node, $scope);
    }

    /**
     * @param MethodCall $node
     * @return RenderTemplateWithParameters[]
     */
    public function findRenderTemplateWithParameters(Node $node, Scope $scope): array
    {
        return $this->latteTemplateWithParametersMatcher->match($node, $scope);
    }

    /**
     * @param MethodCall $node
     * @return ComponentNameAndType[]
     */
    public function findComponentNamesAndTypes(Node $node, Scope $scope): array
    {
        return $this->componentMapResolver->resolveFromMethodCall($node, $scope);
    }
}
