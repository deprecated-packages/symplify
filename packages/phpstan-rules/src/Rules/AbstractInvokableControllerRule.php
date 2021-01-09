<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\Astral\Naming\SimpleNameResolver;

abstract class AbstractInvokableControllerRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    private const ROUTE_ATTRIBUTE = Route::class;

    /**
     * @var SimpleNameResolver
     */
    protected $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    protected function isInControllerClass(Scope $scope): bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return false;
        }

        // skip
        if (is_a($className, 'EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController', true)) {
            return false;
        }

        return is_a($className, AbstractController::class, true);
    }

    protected function getRouteAttribute(ClassMethod $classMethod): ?FullyQualified
    {
        /** @var AttributeGroup $attrGroup */
        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if (! $attribute->name instanceof FullyQualified) {
                    continue;
                }

                $attributeClass = $this->simpleNameResolver->getName($attribute->name);
                if ($attributeClass === self::ROUTE_ATTRIBUTE) {
                    return $attribute->name;
                }
            }
        }

        return null;
    }
}
