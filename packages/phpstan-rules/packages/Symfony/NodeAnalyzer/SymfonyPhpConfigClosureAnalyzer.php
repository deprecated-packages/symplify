<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Name;

final class SymfonyPhpConfigClosureAnalyzer
{
    public function isSymfonyPhpConfig(Closure $closure): bool
    {
        $params = $closure->params;
        if (count($params) !== 1) {
            return false;
        }

        $param = $params[0];
        if (! $param->type instanceof Name) {
            return false;
        }

        $paramType = $param->type->toString();
        return is_a(
            $paramType,
            'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
            true
        );
    }
}
