<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Finder\ConfigFileFinder;

use Symplify\ConfigTransformer\Finder\ConfigFileFinder;
use Symplify\ConfigTransformer\Kernel\ConfigTransformerKernel;
use Symplify\ConfigTransformer\ValueObject\Configuration;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ConfigFileFinderTest extends AbstractKernelTestCase
{
    private ConfigFileFinder $configFileFinder;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigTransformerKernel::class);

        $this->configFileFinder = $this->getService(ConfigFileFinder::class);
    }

    public function test(): void
    {
        $configuration = new Configuration([__DIR__ . '/Fixture'], true);

        $fileInfos = $this->configFileFinder->findFileInfos($configuration);
        $this->assertCount(1, $fileInfos);
    }
}
