<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Container;

use Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerSource\ParameterStorage;

final class ParametersTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $this->container = (new ContainerFactory())->create();
    }

    public function testConstructorParameters()
    {
        /** @var ParameterStorage $parameterStorage */
        $parameterStorage = $this->container->getByType(ParameterStorage::class);
        $this->assertInstanceOf(ParameterStorage::class, $parameterStorage);

        $this->assertSame('1', $parameterStorage->getParameter());
        $this->assertSame([2, 3], $parameterStorage->getGroupOfParameters());
    }

    public function testBundleParameters()
    {
        /** @var Loader $loader */
        $loader = $this->container->getByType(Loader::class);
        $this->assertInstanceOf(Loader::class, $loader);

        $fakerProcessorMethod = $loader->getFakerProcessorMethod();
        $this->assertSame(
            'cs_CZ',
            PHPUnit_Framework_Assert::getObjectAttribute($fakerProcessorMethod, 'defaultLocale')
        );
    }
}
