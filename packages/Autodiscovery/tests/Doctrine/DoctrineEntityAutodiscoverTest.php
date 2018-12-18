<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManager;
use Symplify\Autodiscovery\Tests\AbstractContainerAwareTestCase;
use Symplify\Autodiscovery\Tests\KernelProjectDir\Entity\Product;

/**
 * @covers \Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer
 */
final class DoctrineEntityAutodiscoverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var MappingDriver
     */
    private $mappingDriver;

    protected function setUp(): void
    {
        /** @var Registry $registry */
        $registry = $this->container->get('doctrine');

        /** @var EntityManager $entityManager */
        $entityManager = $registry->getManager();
        $configuration = $entityManager->getConfiguration();

        $this->mappingDriver = $configuration->getMetadataDriverImpl();
    }

    public function test(): void
    {
        $this->assertInstanceOf(MappingDriverChain::class, $this->mappingDriver);
        $this->assertNotEmpty($this->mappingDriver->getAllClassNames());

        $this->assertFalse($this->mappingDriver->isTransient(Product::class));
        $this->assertTrue($this->mappingDriver->isTransient('NonExisting'));
    }
}
