<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Markdown;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\Markdown\MarkdownDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class MarkdownDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var MarkdownDecorator
     */
    private $markdownDecorator;

    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
        $this->configuration->loadFromArray([
            'configuration' => [
                Configuration::OPTION_MARKDOWN_HEADLINE_ANCHORS => false,
            ],
        ]);

        $this->markdownDecorator = $this->container->get(MarkdownDecorator::class);
    }

    public function testNotMarkdown(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.latte');
        $this->markdownDecorator->decorateFile($file);

        $this->assertContains('# Content...', $file->getContent());
    }

    public function testMarkdown(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.md');
        $this->markdownDecorator->decorateFile($file);

        $this->assertContains('<h1>Content...</h1>', $file->getContent());
    }

    public function testMarkdownWithAnchors(): void
    {
        $this->configuration->loadFromArray([
            'configuration' => [
                Configuration::OPTION_MARKDOWN_HEADLINE_ANCHORS => true,
            ],
        ]);

        $file = $this->createFileFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.md');
        $this->markdownDecorator->decorateFile($file);

        $this->assertSame(
            '<h1 id="content"><a class="anchor" href="#content" aria-hidden="true">' .
            '<span class="anchor-icon">#</span></a>Content...</h1>',
            $file->getContent()
        );
    }

    private function createFileFromFilePath(string $filePath): AbstractFile
    {
        $fileInfo = new SplFileInfo($filePath);

        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory('sourceDirectory');

        return (new FileFactory($configuration))->create($fileInfo);
    }
}
