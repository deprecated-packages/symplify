<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Configuration;

use Iterator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Renderable\ConfigurationFileDecorator;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use function Safe\sprintf;

final class ConfigurationFileDecoratorTest extends AbstractContainerAwareTestCase
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
        $this->configurationFileDecorator = $this->container->get(ConfigurationFileDecorator::class);
        $this->fileFactory = $this->container->get(FileFactory::class);
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
