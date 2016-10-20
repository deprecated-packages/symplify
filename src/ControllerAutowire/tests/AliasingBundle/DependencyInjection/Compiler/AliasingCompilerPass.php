<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\AliasingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is done by JMSDiExtraBundle.
 *
 * @see http://git.io/vu3qZ
 *
 * And might be done by others of course.
 *
 * It disables decoration for controller_resolver
 */
final class AliasingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->setAlias('controller_resolver', new Alias('some_alias.controller_resolver', false));
    }
}
