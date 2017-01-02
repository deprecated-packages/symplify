<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\DoctrineBundle;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;

final class InitTest extends TestCase
{
    public function test()
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/config/init.neon');

        /** @var Configuration $configuration */
        $configuration = $container->getByType(Configuration::class);
        $this->assertInstanceOf(Configuration::class, $configuration);

        $this->assertSame(ClassMetadataFactory::class, $configuration->getClassMetadataFactoryName());
        $this->assertInstanceOf(MappingDriverChain::class, $configuration->getMetadataDriverImpl());
    }
}
