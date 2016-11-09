<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Configuration;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\FileFactory;

final class ConfigurationDecoratorTest extends TestCase
{
    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    protected function setUp()
    {
        $this->configurationDecorator = new ConfigurationDecorator(
            new NeonParser()
        );
    }

    /**
     * @dataProvider provideDataForDecorateFile()
     */
    public function testDecorateFile(string $filePath, string $fileContent, array $expectedConfiguration)
    {
        $fileInfo = new SplFileInfo($filePath);
        $configuration = new Configuration(new NeonParser());
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
