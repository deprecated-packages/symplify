<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Configuration;

use SplFileInfo;
use Symplify\Statie\Exception\Neon\InvalidNeonSyntaxException;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
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
        $file = $this->fileFactory->create($fileInfo);

        $this->assertSame([], $file->getConfiguration());
        $this->assertNotSame($fileContent, $file->getContent());

        $this->configurationDecorator->decorateFile($file);

        $this->assertSame($fileContent, $file->getContent());
        $this->assertSame($expectedConfiguration, $file->getConfiguration());
    }

    public function testDecorateFileWithInvalidNeonSyntax(): void
    {
        $brokenNeonFilePath = __DIR__ . '/ConfigurationDecoratorSource/someFileWithBrokenConfigurationSyntax.latte';
        $fileInfo = new SplFileInfo($brokenNeonFilePath);
        $file = $this->fileFactory->create($fileInfo);

        $this->expectException(InvalidNeonSyntaxException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid NEON syntax found in "%s" file: Bad indentation on line 2, column 3.', $brokenNeonFilePath
        ));

        $this->configurationDecorator->decorateFile($file);
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
        ];
    }
}
