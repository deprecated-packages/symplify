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
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\Nette\NodeVisitor\AssignedParametersVisitor;
use Symplify\PHPStanRules\Nette\NodeVisitor\RenderParametersVisitor;
use Symplify\PHPStanRules\Nette\NodeVisitor\TemplatePathFinderVisitor;
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

    public function match(MethodCall $methodCall, Scope $scope): ?RenderTemplateWithParameters
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }

        $templates = $this->findTemplates($class, $scope);
        if ($templates === []) {
            return null;
        }

        $parameters = $this->findParameters($class, $scope);
        return new RenderTemplateWithParameters($templates, new Array_($parameters));
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
