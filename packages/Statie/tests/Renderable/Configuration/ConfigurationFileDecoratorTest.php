<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Configuration;

use Iterator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\ConfigurationFileDecorator;
use Symplify\Statie\Renderable\File\FileFactory;

final class ConfigurationFileDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var ConfigurationFileDecorator
     */
    private $configurationFileDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        $this->configurationFileDecorator = self::$container->get(ConfigurationFileDecorator::class);
        $this->fileFactory = self::$container->get(FileFactory::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/ConfigurationFileDecoratorSource');
    }

    /**
     * @dataProvider provideDataForDecorateFile()
     * @param mixed[] $expectedConfiguration
     */
    public function testDecorateFile(string $filePath, string $fileContent, array $expectedConfiguration): void
    {
        $fileInfo = new SmartFileInfo($filePath);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->assertSame([], $file->getConfiguration());

        $this->configurationFileDecorator->decorateFiles([$file]);

        $this->assertSame($fileContent, $file->getContent());
        $this->assertSame($expectedConfiguration, $file->getConfiguration());
    }

    public function testInvalidYamlSyntax(): void
    {
        $brokenYamlFilePath = __DIR__ . '/ConfigurationFileDecoratorSource/someFileWithBrokenConfigurationSyntax.latte';
        $fileInfo = new SmartFileInfo($brokenYamlFilePath);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(sprintf('Invalid YAML syntax found in "%s": '
        . 'A colon cannot be used in an unquoted mapping value at line 2 (near "  another_key: value") at line 2.', $brokenYamlFilePath));

        $this->configurationFileDecorator->decorateFiles([$file]);
    }

    public function provideDataForDecorateFile(): Iterator
    {
        yield [__DIR__ . '/ConfigurationFileDecoratorSource/someFile.latte', 'Content...', [
            'key' => 'value',
        ]];
        yield [__DIR__ . '/ConfigurationFileDecoratorSource/someFileWithEmptyConfig.latte', 'Content...', []];
        yield [__DIR__ . '/ConfigurationFileDecoratorSource/someFileWithNoConfig.latte', 'Content...', []];
    }
}
