<?php declare(strict_types=1);

namespace Symplify\DoctrineMigrations\Tests\EventSubscriber;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symplify\DoctrineMigrations\Tests\ContainerFactory;

abstract class AbstractEventSubscriberTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Application
     */
    protected $application;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->create();
        $this->container = $container;
        $this->application = $container->getByType(Application::class);
    }
}
