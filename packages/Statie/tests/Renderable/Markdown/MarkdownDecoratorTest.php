<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Markdown;

use ParsedownExtra;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\File;
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
        $this->markdownDecorator = new MarkdownDecorator(new ParsedownExtra());
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

    private function createFileFromFilePath(string $filePath) : File
    {
        $fileInfo = new SplFileInfo($filePath);

        $configuration = new Configuration(new NeonParser());
        $configuration->setSourceDirectory('sourceDirectory');

        return (new FileFactory($configuration))->create($fileInfo);
    }
}
