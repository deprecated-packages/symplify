<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\LatteTemplateHolder;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanLatteRules\Contract\LatteTemplateHolderInterface;
use Symplify\PHPStanLatteRules\NodeAnalyzer\LatteTemplateWithParametersMatcher;
use Symplify\PHPStanLatteRules\TypeAnalyzer\ComponentMapResolver;

final class NetteApplicationUIControl implements LatteTemplateHolderInterface
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
        if ($this->simpleNameResolver->getName($classMethod) !== 'render') {
            return false;
        }

        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (! $class instanceof Class_) {
            return false;
        }
        $className = $this->simpleNameResolver->getName($class);
        $classObject = new ObjectType($className);
        return $classObject->isInstanceOf('Nette\Application\UI\Control')
            ->yes();
    }

    public function findRenderTemplateWithParameters(ClassMethod $classMethod, Scope $scope): array
    {
        return $this->latteTemplateWithParametersMatcher->match($classMethod, $scope);
    }

    public function findComponentNamesAndTypes(ClassMethod $classMethod, Scope $scope): array
    {
        return $this->componentMapResolver->resolveFromClassMethod($classMethod, $scope);
    }
}
