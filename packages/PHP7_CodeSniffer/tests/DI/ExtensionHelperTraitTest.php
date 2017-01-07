<?php

namespace Symplify\PHP7_CodeSniffer\Tests\DI;

use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\PHP7_CodeSniffer\Tests\DI\ExtensionHelperTraitSource\Collected;
use Symplify\PHP7_CodeSniffer\Tests\DI\ExtensionHelperTraitSource\Collector;
use Symplify\PHP7_CodeSniffer\Tests\DI\ExtensionHelperTraitSource\ExtensionWithTrait;

final class ExtensionHelperTraitTest extends TestCase
{
    /**
     * @var ExtensionWithTrait
     */
    private $extensionWithTrait;

    protected function setUp()
    {
        $this->extensionWithTrait = new ExtensionWithTrait();
        $this->extensionWithTrait->setCompiler(
            new Compiler(new ContainerBuilder()),
            'extensionWithTrait'
        );
    }

    public function testAddServicesToCollector()
    {
        $containerBuilder = $this->extensionWithTrait->getContainerBuilder();

        $collectorDefinition = new ServiceDefinition();
        $collectorDefinition->setClass(Collector::class);
        $containerBuilder->addDefinition('collector', $collectorDefinition);

        $collectedDefinition = new ServiceDefinition();
        $collectedDefinition->setClass(Collected::class);
        $containerBuilder->addDefinition('collected', $collectedDefinition);

        $this->assertSame([], $collectorDefinition->getSetup());

        $this->extensionWithTrait->addServicesToCollector(
            Collector::class,
            Collected::class,
            'add'
        );

        $this->assertNotSame([], $collectorDefinition->getSetup());
        $this->assertEquals(
            new Statement('add', ['@collected']),
            $collectorDefinition->getSetup()[0]
        );
    }

    public function testGetDefinitionByType()
    {
        $containerBuilder = $this->extensionWithTrait->getContainerBuilder();

        $collectorDefinition = new ServiceDefinition();
        $collectorDefinition->setClass(stdClass::class);
        $containerBuilder->addDefinition('class', $collectorDefinition);

        $definition = $this->extensionWithTrait->getDefinitionByType(stdClass::class);
        $this->assertSame(stdClass::class, $definition->getClass());
    }
}
