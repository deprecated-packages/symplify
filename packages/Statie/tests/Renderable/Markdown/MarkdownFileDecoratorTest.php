<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Markdown;

use Iterator;
use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;
use Symplify\Statie\Configuration\Configuration;
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
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
        $this->markdownFileDecorator = $this->container->get(MarkdownFileDecorator::class);

        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    /**
     * @dataProvider provideFilesToHtml()
     */
    public function testNotMarkdown(string $file, string $expectedContent, string $message): void
    {
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath($file);
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
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath(__DIR__ . '/MarkdownFileDecoratorSource/someFile.md');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $file->addConfiguration([
            'perex' => '**Hey**',
        ]);

        $this->markdownFileDecorator->decorateFiles([$file]);

        $this->assertSame('<strong>Hey</strong>', $file->getConfiguration()['perex']);
    }
}
