<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Name;
use Symplify\Astral\Naming\SimpleNameResolver;

final class SymfonyPhpConfigClosureAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

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

        $paramType = $this->simpleNameResolver->getName($param->type);
        if (! is_string($paramType)) {
            return false;
        }

        return is_a(
            $paramType,
            'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
            true
        );
    }
}
