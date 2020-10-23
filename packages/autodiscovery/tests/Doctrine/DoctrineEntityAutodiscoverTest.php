<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Symplify\Autodiscovery\Tests\Source\HttpKernel\AudiscoveryTestingKernel;
use Symplify\Autodiscovery\Tests\Source\KernelProjectDir\Entity\Product;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

/**
 * @covers \Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer
 */
final class DoctrineEntityAutodiscoverTest extends AbstractKernelTestCase
{
    /**
     * @var MappingDriver
     */
    private $mappingDriver;

    protected function setUp(): void
    {
        $this->bootKernel(AudiscoveryTestingKernel::class);

        /** @var Registry $registry */
        $registry = static::$container->get('doctrine');

        /** @var EntityManager $entityManager */
        $entityManager = $registry->getManager();
        $configuration = $entityManager->getConfiguration();

        $mappingDriver = $configuration->getMetadataDriverImpl();
        assert($mappingDriver !== null);

        $this->mappingDriver = $mappingDriver;
    }

    public function test(): void
    {
        $entityClasses = [
            Product::class,
            'Kedlubna\Component\Tagging\Context\Context',
            'Kedlubna\Component\Tagging\Tag\Tag',
        ];
        sort($entityClasses);

        $classNames = $this->mappingDriver->getAllClassNames();
        sort($classNames);

        $this->assertSame($entityClasses, $classNames);
    }
}
