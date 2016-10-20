<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Transformer;

use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\NetteAdapterForSymfonyBundles\Transformer\ArgumentsTransformer;

final class ArgumentTransformerTest extends TestCase
{
    /**
     * @var ArgumentsTransformer
     */
    private $argumentsTransformer;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp()
    {
        $this->argumentsTransformer = new ArgumentsTransformer();
        $this->containerBuilder = new ContainerBuilder();
        $this->argumentsTransformer->setContainerBuilder($this->containerBuilder);

        $this->containerBuilder->addDefinition('someService')
            ->setClass(stdClass::class);
    }

    public function testReferences()
    {
        $symfonyArguments = [new Reference('name')];
        $netteArguments = $this->argumentsTransformer->transformFromSymfonyToNette($symfonyArguments);
        $this->assertSame(['@name'], $netteArguments);

        $symfonyArguments = [new Reference('@stdClass')];
        $netteArguments = $this->argumentsTransformer->transformFromSymfonyToNette($symfonyArguments);
        $this->assertSame(['@someService'], $netteArguments);
    }
}
