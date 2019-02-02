<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new LatteToTwigConverterKernel();
        $appKernel->boot();

        // this is require to keep CLI verbosity independent on AppKernel dev/prod mode
        putenv('SHELL_VERBOSITY=0');

        return $appKernel->getContainer();
    }
}
