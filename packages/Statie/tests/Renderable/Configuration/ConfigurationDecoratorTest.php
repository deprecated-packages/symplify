<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Configuration;

use Iterator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;
use Symplify\Statie\Renderable\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class ConfigurationDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->configurationDecorator = $this->container->get(ConfigurationDecorator::class);
        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    /**
     * @dataProvider provideDataForDecorateFile()
     * @param mixed[] $expectedConfiguration
     */
    public function testDecorateFile(string $filePath, string $fileContent, array $expectedConfiguration): void
    {
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath($filePath);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->assertSame([], $file->getConfiguration());

        $this->configurationDecorator->decorateFiles([$file]);

        $this->assertSame($fileContent, $file->getContent());
        $this->assertSame($expectedConfiguration, $file->getConfiguration());
    }

    public function testInvalidYamlSyntax(): void
    {
        $brokenYamlFilePath = __DIR__ . '/ConfigurationDecoratorSource/someFileWithBrokenConfigurationSyntax.latte';
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath($brokenYamlFilePath);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid YAML syntax found in "%s": '
            . 'A colon cannot be used in an unquoted mapping value at line 2 (near "  another_key: value").',
            $brokenYamlFilePath
        ));

        $this->configurationDecorator->decorateFiles([$file]);
    }

    public function provideDataForDecorateFile(): Iterator
    {
        yield [__DIR__ . '/ConfigurationDecoratorSource/someFile.latte', 'Content...', [
            'key' => 'value',
        ]];
        yield [__DIR__ . '/ConfigurationDecoratorSource/someFileWithEmptyConfig.latte', 'Content...', []];
        yield [__DIR__ . '/ConfigurationDecoratorSource/someFileWithNoConfig.latte', 'Content...', []];
    }
}
