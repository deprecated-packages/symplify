<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;
use Symplify\BetterPhpDocParser\HttpKernel\BetterPhpDocParserKernel;

final class ContainerFactory
{
    /**
     * @return ContainerInterface|SymfonyContainerInterface|Container
     */
    public function create(): ContainerInterface
    {
        $appKernel = new BetterPhpDocParserKernel();
        $appKernel->boot();

        // this is require to keep CLI verbosity independent on AppKernel dev/prod mode
        putenv('SHELL_VERBOSITY=0');

        return $appKernel->getContainer();
    }
}
