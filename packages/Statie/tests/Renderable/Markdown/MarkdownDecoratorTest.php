<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Markdown;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\Markdown\MarkdownFileDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class MarkdownDecoratorTest extends AbstractContainerAwareTestCase
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
        $this->configuration->disableMarkdownHeadlineAnchors();
        $this->markdownFileDecorator = $this->container->get(MarkdownFileDecorator::class);

        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    public function testNotMarkdown(): void
    {
        $file = $this->fileFactory->createFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.latte');
        $this->markdownFileDecorator->decorateFiles([$file]);

        $this->assertContains('# Content...', $file->getContent());
    }

    public function testMarkdown(): void
    {
        $file = $this->fileFactory->createFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.md');
        $this->markdownFileDecorator->decorateFiles([$file]);

        $this->assertContains('<h1>Content...</h1>', $file->getContent());
    }

    public function testMarkdownWithAnchors(): void
    {
        $this->configuration->enableMarkdownHeadlineAnchors();

        $file = $this->fileFactory->createFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.md');
        $this->markdownFileDecorator->decorateFiles([$file]);

        $this->assertSame(
            '<h1 id="content"><a class="anchor" href="#content" aria-hidden="true">' .
            '<span class="anchor-icon">#</span></a>Content...</h1>',
            $file->getContent()
        );
    }
}
