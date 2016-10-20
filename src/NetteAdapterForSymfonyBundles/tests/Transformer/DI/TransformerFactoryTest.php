<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Transformer\DI;

use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;
use Symplify\NetteAdapterForSymfonyBundles\Transformer\ContainerBuilderTransformer;
use Symplify\NetteAdapterForSymfonyBundles\Transformer\DI\TransformerFactory;

final class TransformerFactoryTest extends TestCase
{
    public function testWithApplicationExtension()
    {
        $transformerFactory = new TransformerFactory(
            new ContainerBuilder(),
            ContainerFactory::createAndReturnTempDir()
        );

        $transformer = $transformerFactory->create();
        $containerBuilderTransformer = $transformer->getByType(ContainerBuilderTransformer::class);
        $this->assertInstanceOf(ContainerBuilderTransformer::class, $containerBuilderTransformer);
    }
}
