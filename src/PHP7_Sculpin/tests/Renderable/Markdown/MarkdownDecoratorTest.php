<?php

namespace Symplify\PHP7_Sculpin\Renderable\Markdown;

use Michelf\MarkdownExtra;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;

final class MarkdownDecoratorTest extends TestCase
{
    /**
     * @var MarkdownDecorator
     */
    private $markdownDecorator;

    protected function setUp()
    {
        $this->markdownDecorator = new MarkdownDecorator(new MarkdownExtra());
    }

    public function testNotMarkdown()
    {
        $file = $this->createFileFromFilePath(__DIR__.'/MarkdownDecoratorSource/someFile.latte');
        $this->markdownDecorator->decorateFile($file);

        $this->assertSame('# Content...'.PHP_EOL, $file->getContent());
    }

    public function testMarkdown()
    {
        $file = $this->createFileFromFilePath(__DIR__.'/MarkdownDecoratorSource/someFile.md');
        $this->markdownDecorator->decorateFile($file);

        $this->assertSame('<h1>Content...</h1>'.PHP_EOL, $file->getContent());
    }

    private function createFileFromFilePath(string $filePath) : File
    {
        $fileInfo = new SplFileInfo($filePath);

        return (new FileFactory('sourceDirectory'))->create($fileInfo);
    }
}
