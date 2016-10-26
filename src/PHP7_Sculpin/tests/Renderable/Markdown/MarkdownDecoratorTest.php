<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable\Markdown;

use Michelf\MarkdownExtra;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;
use Symplify\PHP7_Sculpin\Renderable\Markdown\MarkdownDecorator;

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

        $configuration = new Configuration(new YamlAndNeonParser());
        $configuration->setSourceDirectory('sourceDirectory');

        return (new FileFactory($configuration))->create($fileInfo);
    }
}
