<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\DoctrineBundle;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;

final class InitTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $this->container = (new ContainerFactory())->createWithConfig(__DIR__ . '/config/init.neon');
    }

    public function testGetService()
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->getByType(Configuration::class);
        $this->assertInstanceOf(Configuration::class, $configuration);

        $this->assertSame(ClassMetadataFactory::class, $configuration->getClassMetadataFactoryName());
        $this->assertInstanceOf(MappingDriverChain::class, $configuration->getMetadataDriverImpl());
    }
}
