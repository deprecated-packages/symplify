<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Container;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;

final class FactoryTest extends TestCase
{
    public function testFactory()
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__.'/FactorySource/config/init.neon');

        /** @var EntityManager $entityManager */
        $entityManager = $container->getByType(EntityManager::class);
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }
}
