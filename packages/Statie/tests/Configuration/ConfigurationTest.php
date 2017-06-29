<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class ConfigurationTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
    }

    public function testAddGlobalVariable(): void
    {
        $this->configuration->addGlobalVarialbe('key', 'value');

        $this->assertSame([
            'key' => 'value',
        ], $this->configuration->getOptions());
    }

    public function testLoadFromFiles(): void
    {
        $neonConfigFile = new SplFileInfo(__DIR__ . '/ConfigurationSource/config.neon');
        $yamlConfigFile = new SplFileInfo(__DIR__ . '/ConfigurationSource/config.yaml');
        $this->configuration->loadFromFiles([$neonConfigFile, $yamlConfigFile]);

        $this->assertSame([
            'key' => 'value',
            'another_key' => 'another_value',
        ], $this->configuration->getOptions());
    }
}
