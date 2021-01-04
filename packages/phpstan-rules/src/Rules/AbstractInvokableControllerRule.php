<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PHPStan\Analyser\Scope;

abstract class AbstractInvokableControllerRule extends AbstractSymplifyRule
{
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
}
