<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;

final class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = new Configuration(new NeonParser);
    }

    public function testAddGlobalVariable(): void
    {
        $this->configuration->addGlobalVarialbe('key', 'value');

        $this->assertSame([
            'key' => 'value',
        ], $this->configuration->getGlobalVariables());
    }

    public function testLoadFromFiles(): void
    {
        $neonConfigFile = new SplFileInfo(__DIR__ . '/ConfigurationSource/config.neon');
        $yamlConfigFile = new SplFileInfo(__DIR__ . '/ConfigurationSource/config.yaml');
        $this->configuration->loadFromFiles([$neonConfigFile, $yamlConfigFile]);

        $this->assertSame([
            'key' => 'value',
            'another_key' => 'another_value',
        ], $this->configuration->getGlobalVariables());
    }
}
