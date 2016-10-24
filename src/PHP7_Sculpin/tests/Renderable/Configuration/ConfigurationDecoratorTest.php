<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable\Configuration;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
use Symplify\PHP7_Sculpin\Renderable\Configuration\ConfigurationDecorator;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;

final class ConfigurationDecoratorTest extends TestCase
{
    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    protected function setUp()
    {
        $this->configurationDecorator = new ConfigurationDecorator(
            new YamlAndNeonParser()
        );
    }

    /**
     * @dataProvider provideDataForDecorateFile()
     */
    public function testDecorateFile(string $filePath, string $fileContent, array $expectedConfiguration)
    {
        $fileInfo = new SplFileInfo($filePath);
        $configuration = new Configuration(new YamlAndNeonParser());
        $configuration->setSourceDirectory('sourceDirectory');
        $filePath = (new FileFactory($configuration))->create($fileInfo);

        $this->assertSame([], $filePath->getConfiguration());
        $this->assertNotSame($fileContent, $filePath->getContent());

        $this->configurationDecorator->decorateFile($filePath);

        $this->assertSame($fileContent, $filePath->getContent());
        $this->assertSame($expectedConfiguration, $filePath->getConfiguration());
    }

    public function provideDataForDecorateFile() : array
    {
        return [
            [__DIR__ . '/ConfigurationDecoratorSource/someFile.latte', 'Content...', [
                'key' => 'value',
            ]],
        ];
    }
}
