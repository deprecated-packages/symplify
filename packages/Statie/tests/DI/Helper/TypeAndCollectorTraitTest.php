<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\DI\Helper;

use Nette\DI\Compiler;
use Nette\DI\ServiceDefinition;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Tests\DI\Helper\TypeAndCollectorTraitSource\CollectedClass;
use Symplify\Statie\Tests\DI\Helper\TypeAndCollectorTraitSource\CollectorClass;
use Symplify\Statie\Tests\DI\Helper\TypeAndCollectorTraitSource\SmartExtension;

final class TypeAndCollectorTraitTest extends TestCase
{
    public function testCollectByType()
    {
        $smartExtension = $this->prepareAndReturnExtension();
        $smartExtension->collectByType(CollectorClass::class, CollectedClass::class, 'setterMethod');

        $containerBuilder = $smartExtension->getContainerBuilder();
        $collectorDefinition = $containerBuilder->getDefinition('collector');

        $this->assertSame('setterMethod', $collectorDefinition->getSetup()[0]->entity);
        $this->assertSame(['@collected'], $collectorDefinition->getSetup()[0]->arguments);
    }

    public function testGetDefinitionByType()
    {
        $smartExtension = $this->prepareAndReturnExtension();
        $collectorDefinition = $smartExtension->getDefinitionByType(CollectorClass::class);

        $this->assertSame(CollectorClass::class, $collectorDefinition->getClass());
    }

    private function prepareAndReturnExtension() : SmartExtension
    {
        $smartExtension = new SmartExtension();
        $smartExtension->setCompiler(new Compiler(), null);

        $containerBuilder = $smartExtension->getContainerBuilder();
        $containerBuilder->addDefinition('collector', (new ServiceDefinition())->setClass(CollectorClass::class));
        $containerBuilder->addDefinition('collected', (new ServiceDefinition())->setClass(CollectedClass::class));

        return $smartExtension;
    }
}
