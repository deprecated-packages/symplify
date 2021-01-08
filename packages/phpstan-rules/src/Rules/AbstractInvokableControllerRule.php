<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;

abstract class AbstractInvokableControllerRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    private const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Annotation\Route';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    protected function isInControllerClass(Scope $scope): bool
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return false;
        }

        // skip
        if (is_a($className, 'EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController', true)) {
            return false;
        }

        return is_a($className, 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController', true);
    }

    protected function getRouteAttribute(ClassMethod $node): ?FullyQualified
    {
        /** @var AttributeGroup $attrGroup */
        foreach ((array) $node->attrGroups as $attrGroup) {
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
