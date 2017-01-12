<?php declare(strict_types=1);

namespace Symplify\DoctrineFilters\Tests\DI;

use Doctrine\ORM\Configuration;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineFilters\Contract\FilterManagerInterface;
use Symplify\DoctrineFilters\DI\FiltersExtension;
use Symplify\DoctrineFilters\FilterManager;
use Symplify\DoctrineFilters\Tests\FilterManager\Source\ActiveFilter;

final class FiltersExtensionTest extends TestCase
{
    public function testLoadConfiguration()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $definition = $containerBuilder->getDefinition($containerBuilder->getByType(FilterManagerInterface::class));
        $this->assertSame(FilterManager::class, $definition->getClass());
    }

    public function testBeforeCompile()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();
        $containerBuilder->addDefinition('ormConfiguration')
            ->setClass(Configuration::class)
            ->setAutowired(false);

        $containerBuilder->addDefinition('filter')
            ->setClass(ActiveFilter::class);

        $extension->beforeCompile();

        $filterManagerDefinition = $containerBuilder->getDefinition(
            $containerBuilder->getByType(FilterManagerInterface::class)
        );
        $this->assertSame('addFilter', $filterManagerDefinition->getSetup()[0]->getEntity());
        $this->assertSame(['filter', '@filter'], $filterManagerDefinition->getSetup()[0]->arguments);
    }

    private function getExtension() : FiltersExtension
    {
        $extension = new FiltersExtension;
        $extension->setCompiler(new Compiler(new ContainerBuilder), 'filters');
        return $extension;
    }
}
