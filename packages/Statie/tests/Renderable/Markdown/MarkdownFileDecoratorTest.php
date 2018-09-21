<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Markdown;

use Iterator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\MarkdownFileDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class MarkdownFileDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var MarkdownFileDecorator
     */
    private $markdownFileDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->markdownFileDecorator = $this->container->get(MarkdownFileDecorator::class);

        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    /**
     * @dataProvider provideFilesToHtml()
     */
    public function testNotMarkdown(string $file, string $expectedContent, string $message): void
    {
        $fileInfo = new SmartFileInfo($file);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->markdownFileDecorator->decorateFiles([$file]);

        $this->assertContains($expectedContent, $file->getContent(), $message);
    }

    public function provideFilesToHtml(): Iterator
    {
        yield [
            __DIR__ . '/MarkdownFileDecoratorSource/someFile.latte',
            '# Content...',
            'No conversion with ".latte" suffix',
        ];
        yield [
            __DIR__ . '/MarkdownFileDecoratorSource/someFile.md',
            '<h1>Content...</h1>',
            'Conversion thanks to ".md" suffix',
        ];
    }

    public function testMarkdownPerex(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/MarkdownFileDecoratorSource/someFile.md');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $file->addConfiguration([
            'perex' => '**Hey**',
        ]);

        $this->markdownFileDecorator->decorateFiles([$file]);

        $this->assertSame('<strong>Hey</strong>', $file->getConfiguration()['perex']);
    }
}
