<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Markdown;

use ParsedownExtra;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\Markdown\MarkdownDecorator;

final class MarkdownDecoratorTest extends TestCase
{
    /**
     * @var MarkdownDecorator
     */
    private $markdownDecorator;

    protected function setUp()
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->setMarkdownHeadlineAnchors(false);

        $this->markdownDecorator = new MarkdownDecorator(new ParsedownExtra, $configuration);
    }

    public function testNotMarkdown()
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.latte');
        $this->markdownDecorator->decorateFile($file);

        $this->assertContains('# Content...', $file->getContent());
    }

    public function testMarkdown()
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/MarkdownDecoratorSource/someFile.md');
        $this->markdownDecorator->decorateFile($file);

        $this->assertContains('<h1>Content...</h1>', $file->getContent());
    }

    public function testMarkdownWithAnchors()
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->setMarkdownHeadlineAnchors(true);

        $this->markdownDecorator = new MarkdownDecorator(new ParsedownExtra, $configuration);

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

        $configuration = new Configuration(new NeonParser);
        $configuration->setSourceDirectory('sourceDirectory');

        return (new FileFactory($configuration))->create($fileInfo);
    }
}
