<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\AliasingBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ControllerAutowire\Tests\AliasingBundle\DependencyInjection\AliasingExtension;
use Symplify\ControllerAutowire\Tests\AliasingBundle\DependencyInjection\Compiler\AliasingCompilerPass;

final class AliasingBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new AliasingCompilerPass());
    }

    public function getContainerExtension() : ExtensionInterface
    {
        return new AliasingExtension();
    }
}
