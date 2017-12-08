<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Configuration;

use SplFileInfo;
use Symplify\Statie\Exception\Neon\InvalidNeonSyntaxException;
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
        $fileInfo = new SplFileInfo($filePath);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->assertSame([], $file->getConfiguration());

        $this->configurationDecorator->decorateFiles([$file]);

        $this->assertSame($fileContent, $file->getContent());
        $this->assertSame($expectedConfiguration, $file->getConfiguration());
    }

    public function testDecorateFileWithInvalidNeonSyntax(): void
    {
        $brokenNeonFilePath = __DIR__ . '/ConfigurationDecoratorSource/someFileWithBrokenConfigurationSyntax.latte';
        $fileInfo = new SplFileInfo($brokenNeonFilePath);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->expectException(InvalidNeonSyntaxException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid NEON syntax found in "%s" file: Bad indentation on line 2, column 3.',
            $brokenNeonFilePath
        ));

        $this->configurationDecorator->decorateFiles([$file]);
    }

    /**
     * @return mixed[][]
     */
    public function provideDataForDecorateFile(): array
    {
        return [
            [__DIR__ . '/ConfigurationDecoratorSource/someFile.latte', 'Content...', [
                'key' => 'value',
            ]],
            [__DIR__ . '/ConfigurationDecoratorSource/someFileWithEmptyConfig.latte', 'Content...', []],
            [__DIR__ . '/ConfigurationDecoratorSource/someFileWithNoConfig.latte', 'Content...', []],
        ];
    }
}
