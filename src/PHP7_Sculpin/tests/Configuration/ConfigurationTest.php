<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;

final class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp()
    {
        $this->configuration = new Configuration(new YamlAndNeonParser());
    }

    public function testAddOption()
    {
        $this->configuration->addOption('key', 'value');

        $this->assertSame([
            'key' => 'value',
        ], $this->configuration->getOptions());
    }

    public function testLoadFromFiles()
    {
        $neonConfigFile = new SplFileInfo(__DIR__ . '/ConfigurationSource/config.neon');
        $yamlConfigFile = new SplFileInfo(__DIR__ . '/ConfigurationSource/config.yaml');
        $this->configuration->loadOptionsFromFiles([$neonConfigFile, $yamlConfigFile]);

        $this->assertSame([
            'key' => 'value',
            'another_key' => 'another_value',
        ], $this->configuration->getOptions());
    }
}
