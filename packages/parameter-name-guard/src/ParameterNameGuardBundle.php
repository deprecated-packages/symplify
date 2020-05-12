<?php

declare(strict_types=1);

namespace Symplify\ParameterNameGuard;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ParameterNameGuard\DependencyInjection\CompilerPass\ParameterNameGuardCompilerPass;

final class ParameterNameGuardBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new ParameterNameGuardCompilerPass());
    }
}
