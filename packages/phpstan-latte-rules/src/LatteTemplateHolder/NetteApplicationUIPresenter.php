<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\LatteTemplateHolder;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
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

    public function check(ClassMethod $classMethod, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isNames($classMethod, ['render*', 'action*'])) {
            return false;
        }

        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (! $class instanceof Class_) {
            return false;
        }
        $className = $this->simpleNameResolver->getName($class);
        $classObject = new ObjectType($className);
        return $classObject->isInstanceOf('Nette\Application\UI\Presenter')
            ->yes();
    }

    public function findRenderTemplateWithParameters(ClassMethod $classMethod, Scope $scope): array
    {
        $template = $this->findTemplateFilePath($classMethod, $scope);
        if ($template === null) {
            return [];
        }

        $parameters = $this->latteTemplateWithParametersMatcher->findParameters($classMethod, $scope);
        return [new RenderTemplateWithParameters($template, new Array_($parameters))];
    }

    public function findComponentNamesAndTypes(ClassMethod $classMethod, Scope $scope): array
    {
        return $this->componentMapResolver->resolveFromClassMethod($classMethod, $scope);
    }

    private function findTemplateFilePath(ClassMethod $classMethod, Scope $scope): ?string
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }
        $className = $this->simpleNameResolver->getName($class);
        $shortClassName = $this->simpleNameResolver->resolveShortName($className);
        $presenterName = str_replace('Presenter', '', $shortClassName);

        $methodName = $this->simpleNameResolver->getName($classMethod);
        $actionName = str_replace(['action', 'render'], '', $methodName);
        $actionName = lcfirst($actionName);

        $dir = dirname($scope->getFile());
        $dir = is_dir("${dir}/templates") ? $dir : dirname($dir);

        $templateFileCandidates = [
            "${dir}/templates/${presenterName}/${actionName}.latte",
            "${dir}/templates/${presenterName}.${actionName}.latte",
        ];

        foreach ($templateFileCandidates as $templateFileCandidate) {
            if (file_exists($templateFileCandidate)) {
                return $templateFileCandidate;
            }
        }
        return null;
    }
}
