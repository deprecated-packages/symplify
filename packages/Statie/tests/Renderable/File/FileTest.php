<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\File;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\FileFactory;

final class FileTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->setSourceDirectory('sourceDirectory');

        $this->fileFactory = new FileFactory($configuration);
    }

    public function testGetRelativeSource(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileFactorySource/someFile.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertStringEndsWith('/FileFactorySource/someFile.latte', $file->getRelativeSource());
    }

    public function testGetPrimaryExtension(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileSource/some.html.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertSame('html', $file->getPrimaryExtension());
    }
}
