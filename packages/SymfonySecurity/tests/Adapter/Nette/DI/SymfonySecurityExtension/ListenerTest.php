<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension;

use Nette\Application\Application;
use Symplify\SymfonySecurity\Tests\Adapter\Nette\ContainerFactory;
use Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\AbstractSecurityExtensionTestCase;

final class ListenerTest extends AbstractSecurityExtensionTestCase
{
    /**
     * @expectedException \Nette\Application\AbortException
     */
    public function testDispatch()
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/ListenerSource/config.neon');

        /** @var Application $application */
        $application = $container->getByType(Application::class);
        $application->run();
    }
}
