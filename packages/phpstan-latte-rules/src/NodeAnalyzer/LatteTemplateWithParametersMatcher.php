<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanLatteRules\NodeVisitor\AssignedParametersVisitor;
use Symplify\PHPStanLatteRules\NodeVisitor\RenderParametersVisitor;
use Symplify\PHPStanLatteRules\NodeVisitor\TemplatePathFinderVisitor;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

final class LatteTemplateWithParametersMatcher
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer,
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    /**
     * @return RenderTemplateWithParameters[]
     */
    public function match(MethodCall $methodCall, Scope $scope): array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        $templates = $this->findTemplates($class, $scope);
        if ($templates === []) {
            return [];
        }

        $parameters = $this->findParameters($class, $scope);

        $result = [];
        foreach ($templates as $template) {
            $result[] = new RenderTemplateWithParameters($template, new Array_($parameters));
        }

        return $result;
    }

    /**
     * @return string[]
     */
    private function findTemplates(Class_ $class, Scope $scope): array
    {
        $nodes = [$class];
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

    /**
     * @return ArrayItem[]
     */
    private function findParameters(Class_ $class, Scope $scope): array
    {
        $assignedParametersVisitor = null;
        $renderParametersVisitor = null;
        $nodes = [$class];
        $nodeTraverser = new NodeTraverser();
        $assignedParametersVisitor = new AssignedParametersVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer
        );
        $renderParametersVisitor = new RenderParametersVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer
        );

        $nodeTraverser->addVisitor($assignedParametersVisitor);
        $nodeTraverser->addVisitor($renderParametersVisitor);
        $nodeTraverser->traverse($nodes);

        return array_merge($assignedParametersVisitor->getParameters(), $renderParametersVisitor->getParameters());
    }
}
