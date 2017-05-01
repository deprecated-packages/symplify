<?php declare(strict_types=1);

namespace Symplify\DefaultAutoconfigure;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\DefaultAutoconfigure\DependencyInjection\Compiler\TurnOnAutoconfigureCompilerPass;
use Symplify\DefaultAutoconfigure\DependencyInjection\Extension\RegisterForAutoconfigurationContainerExtension;

final class SymplifyDefaultAutoconfigureBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(
            new TurnOnAutoconfigureCompilerPass, PassConfig::TYPE_BEFORE_OPTIMIZATION, 155
        );
    }

    public function getContainerExtension(): RegisterForAutoconfigurationContainerExtension
    {
        return new RegisterForAutoconfigurationContainerExtension;
    }
}
