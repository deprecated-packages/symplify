<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\TacticianBundle;

use Closure;
use League\Tactician\CommandBus;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use stdClass;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;

final class NetteServiceAliasTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $this->container = (new ContainerFactory())->createWithConfig(__DIR__.'/config/netteServiceAlias.neon');
    }

    public function testSymfonyServiceReferencing()
    {
        /** @var CommandBus $commandBus */
        $commandBus = $this->container->getByType(CommandBus::class);
        $this->assertInstanceOf(CommandBus::class, $commandBus);

        /** @var Closure $middlewareChain */
        $middlewareChain = PHPUnit_Framework_Assert::getObjectAttribute($commandBus, 'middlewareChain');

        $output = $middlewareChain(new stdClass());
        $this->assertInstanceOf(stdClass::class, $output);
    }
}
