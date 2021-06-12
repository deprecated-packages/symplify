<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Symfony;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;

final class SymfonyControllerAnalyzer
{
    /**
     * @var string
     */
    private const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Annotation\Route';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private AttributeFinder $attributeFinder
    ) {
    }

    public function isActionMethod(ClassMethod $classMethod): bool
    {
        if (! $classMethod->isPublic()) {
            return false;
        }

        if ($this->attributeFinder->hasAttribute($classMethod, self::ROUTE_ATTRIBUTE)) {
            return true;
        }

        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return Strings::contains($docComment->getText(), '@Route');
    }

    public function isInControllerClass(Scope $scope): bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return false;
        }

        // skip
        if (is_a($className, 'EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController', true)) {
            return false;
        }

        return is_a($className, 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController', true);
    }
}
