<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanLatteRules\NodeVisitor\AssignedParametersVisitor;
use Symplify\PHPStanLatteRules\NodeVisitor\RenderParametersVisitor;
use Symplify\PHPStanLatteRules\NodeVisitor\TemplatePathFinderVisitor;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

final class LatteTemplateWithParametersMatcher
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer,
        private NodeValueResolver $nodeValueResolver,
        private NodeScopeResolver $nodeScopeResolver,
    ) {
    }

    /**
     * @return RenderTemplateWithParameters[]
     */
    public function match(ClassMethod $method, Scope $scope): array
    {
        $templates = $this->findTemplates($method, $scope);
        if ($templates === []) {
            return [];
        }

        $parameters = $this->findParameters($method, $scope);

        $result = [];
        foreach ($templates as $template) {
            $result[] = new RenderTemplateWithParameters($template, new Array_($parameters));
        }

        return $result;
    }

    /**
     * @return ArrayItem[]
     */
    public function findParameters(ClassMethod $classMethod, Scope $scope): array
    {
        $nodes = [$classMethod];
        $nodeTraverser = new NodeTraverser();
        $assignedParametersVisitor = new AssignedParametersVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer,
            $this->nodeScopeResolver,
        );
        $renderParametersVisitor = new RenderParametersVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer,
            $this->nodeScopeResolver,
        );

        $nodeTraverser->addVisitor($assignedParametersVisitor);
        $nodeTraverser->addVisitor($renderParametersVisitor);
        $nodeTraverser->traverse($nodes);

        return array_merge($assignedParametersVisitor->getParameters(), $renderParametersVisitor->getParameters());
    }

    /**
     * @return string[]
     */
    private function findTemplates(ClassMethod $classMethod, Scope $scope): array
    {
        $nodes = [$classMethod];
        $nodeTraverser = new NodeTraverser();

        $templatePathFinderVisitor = new TemplatePathFinderVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer,
            $this->nodeValueResolver
        );

        $nodeTraverser->addVisitor($templatePathFinderVisitor);
        $nodeTraverser->traverse($nodes);

        return $templatePathFinderVisitor->getTemplatePaths();
    }
}
