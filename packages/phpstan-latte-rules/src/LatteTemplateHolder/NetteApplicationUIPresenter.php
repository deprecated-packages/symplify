<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\LatteTemplateHolder;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Type\ObjectType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanLatteRules\Contract\LatteTemplateHolderInterface;
use Symplify\PHPStanLatteRules\NodeAnalyzer\LatteTemplateWithParametersMatcher;
use Symplify\PHPStanLatteRules\TypeAnalyzer\ComponentMapResolver;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

final class NetteApplicationUIPresenter implements LatteTemplateHolderInterface
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private LatteTemplateWithParametersMatcher $latteTemplateWithParametersMatcher,
        private ComponentMapResolver $componentMapResolver,
    ) {
    }

    public function check(Node $node, Scope $scope): bool
    {
        if (! $node instanceof InClassNode) {
            return false;
        }

        $class = $node->getOriginalNode();
        if (! $class instanceof Class_) {
            return false;
        }
        $className = $this->simpleNameResolver->getName($class);
        if (! $className) {
            return false;
        }

        $classObject = new ObjectType($className);
        return $classObject->isInstanceOf('Nette\Application\UI\Presenter')
            ->yes();
    }

    /**
     * @param InClassNode $node
     */
    public function findRenderTemplateWithParameters(Node $node, Scope $scope): array
    {
        /** @var Class_ $class */
        $class = $node->getOriginalNode();
        $methods = $class->getMethods();

        $templatesAndParameters = [];
        foreach ($methods as $method) {
            if (! $this->simpleNameResolver->isNames($method, ['render*', 'action*'])) {
                continue;
            }
            $template = $this->findTemplateFilePath($method, $scope);
            if ($template === null) {
                continue;
            }

            $parameters = $this->latteTemplateWithParametersMatcher->findParameters($method, $scope);
            if (! isset($templatesAndParameters[$template])) {
                $templatesAndParameters[$template] = [];
            }
            $templatesAndParameters[$template] = array_merge($templatesAndParameters[$template], $parameters);
        }

        $renderTemplatesWithParameters = [];
        foreach ($templatesAndParameters as $template => $parameters) {
            $renderTemplatesWithParameters[] = new RenderTemplateWithParameters($template, new Array_($parameters));
        }
        return $renderTemplatesWithParameters;
    }

    /**
     * @param InClassNode $node
     */
    public function findComponentNamesAndTypes(Node $node, Scope $scope): array
    {
        /** @var Class_ $class */
        $class = $node->getOriginalNode();
        return $this->componentMapResolver->resolveComponentNamesAndTypes($class, $scope);
    }

    private function findTemplateFilePath(ClassMethod $classMethod, Scope $scope): ?string
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }
        $className = $this->simpleNameResolver->getName($class);
        if (! $className) {
            return null;
        }
        $shortClassName = $this->simpleNameResolver->resolveShortName($className);
        $presenterName = str_replace('Presenter', '', $shortClassName);

        $methodName = $this->simpleNameResolver->getName($classMethod);
        if (! $methodName) {
            return null;
        }
        $actionName = str_replace(['action', 'render'], '', $methodName);
        $actionName = lcfirst($actionName);

        $dir = dirname($scope->getFile());
        $dir = is_dir($dir . '/templates') ? $dir : dirname($dir);

        $templateFileCandidates = [
            $dir . '/templates/' . $presenterName . '/' . $actionName . '.latte',
            $dir . '/templates/' . $presenterName . '.' . $actionName . '.latte',
        ];

        foreach ($templateFileCandidates as $templateFileCandidate) {
            if (file_exists($templateFileCandidate)) {
                return $templateFileCandidate;
            }
        }
        return null;
    }
}
