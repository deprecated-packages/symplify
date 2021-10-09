<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Templates;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
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
        $parametersItems = [];
        foreach ($parameters as $key => $value) {
            $parametersItems[] = new ArrayItem($value, new String_($key));
        }
        return new RenderTemplateWithParameters($templates, new Array_($parametersItems));
    }

    private function findTemplates(Class_ $class, Scope $scope): array
    {
        $nodes = [$class];
        $nodeTraverser = new NodeTraverser();
        $templatePathFinderVisitor = new TemplatePathFinderVisitor($scope, $this->netteTypeAnalyzer, $this->nodeValueResolver);

        $nodeTraverser->addVisitor($templatePathFinderVisitor);
        $nodeTraverser->traverse($nodes);

        return $templatePathFinderVisitor->getTemplatePaths();
    }

    private function findParameters(Class_ $class, Scope $scope): array
    {
        $nodes = [$class];
        $nodeTraverser = new NodeTraverser();
        $assignedVariablesVisitor = new AssignedParametersVisitor($scope, $this->netteTypeAnalyzer);
        $parametersAsParameterVisitor = new RenderParametersVisitor($scope, $this->netteTypeAnalyzer, $this->nodeValueResolver);

        $nodeTraverser->addVisitor($assignedVariablesVisitor);
        $nodeTraverser->addVisitor($parametersAsParameterVisitor);
        $nodeTraverser->traverse($nodes);

        return array_merge($assignedVariablesVisitor->getParameters(), $parametersAsParameterVisitor->getParameters());
    }
}
