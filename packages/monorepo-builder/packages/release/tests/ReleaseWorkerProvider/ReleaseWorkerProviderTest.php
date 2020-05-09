<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Tests\ReleaseWorkerProvider;

use Symfony\Component\Yaml\Yaml;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Release\ReleaseWorkerProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ReleaseWorkerProviderTest extends AbstractKernelTestCase
{
    /**
     * @var ReleaseWorkerProvider
     */
    private $releaseWorkerProvider;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/config/all_release_workers.yaml']);

        $this->releaseWorkerProvider = self::$container->get(ReleaseWorkerProvider::class);
    }

    public function testOrderIsSameAsInConfig(): void
    {
        $releaseWorkers = $this->releaseWorkerProvider->provide();
        $this->assertCount(7, $releaseWorkers);

        $configReleaseWorkerOrder = $this->loadConfigReleaseWorkerOrder();
        $releaseWorkerClasses = $this->getClasses($releaseWorkers);

        $this->assertSame($configReleaseWorkerOrder, $releaseWorkerClasses);
    }

    /**
     * @return string[]
     */
    private function loadConfigReleaseWorkerOrder(): array
    {
        $parsedConfig = Yaml::parseFile(__DIR__ . '/config/all_release_workers.yaml');

        return array_keys($parsedConfig['services']);
    }

    /**
     * @param object[] $objects
     * @return string[]
     */
    private function getClasses(array $objects): array
    {
        $classes = [];
        foreach ($objects as $object) {
            $classes[] = get_class($object);
        }

        return $classes;
    }
}
