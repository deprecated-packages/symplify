<?php

namespace Symplify\PHP7_Sculpin\Renderable\Configuration;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
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
        $filePath = (new FileFactory('sourceDirectory'))->create($fileInfo);

        $this->assertSame([], $filePath->getConfiguration());
        $this->assertNotSame($fileContent, $filePath->getContent());

        $this->configurationDecorator->decorateFile($filePath);

        $this->assertSame($fileContent, $filePath->getContent());
        $this->assertSame($expectedConfiguration, $filePath->getConfiguration());
    }

    public function provideDataForDecorateFile() : array
    {
        return [
            [__DIR__.'/ConfigurationDecoratorSource/someFile.latte', 'Content...', [
                'key' => 'value',
            ]],
        ];
    }
}
