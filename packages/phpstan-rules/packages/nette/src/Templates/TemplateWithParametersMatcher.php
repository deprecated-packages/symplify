<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Templates;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\NetteTypeAnalyzer;
use Symplify\PHPStanRules\Nette\NodeVisitor\AssignedParametersVisitor;
use Symplify\PHPStanRules\Nette\NodeVisitor\RenderParametersVisitor;
use Symplify\PHPStanRules\Nette\NodeVisitor\TemplatePathFinderVisitor;
use Symplify\PHPStanRules\Symfony\ValueObject\RenderTemplateWithParameters;

final class TemplateWithParametersMatcher
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
        $nodes = [$class];
        $nodeTraverser = new NodeTraverser();
        $assignedVariablesVisitor = new AssignedParametersVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer
        );
        $parametersAsParameterVisitor = new RenderParametersVisitor(
            $scope,
            $this->simpleNameResolver,
            $this->netteTypeAnalyzer
        );

        $nodeTraverser->addVisitor($assignedVariablesVisitor);
        $nodeTraverser->addVisitor($parametersAsParameterVisitor);
        $nodeTraverser->traverse($nodes);

        return array_merge($assignedVariablesVisitor->getParameters(), $parametersAsParameterVisitor->getParameters());
    }
}
