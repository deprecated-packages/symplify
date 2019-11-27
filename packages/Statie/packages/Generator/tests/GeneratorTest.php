<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use DateTimeInterface;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\HttpKernel\StatieKernel;

/**
 * @covers \Symplify\Statie\Generator\Generator
 */
final class GeneratorTest extends AbstractGeneratorTest
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(StatieKernel::class, [__DIR__ . '/GeneratorSource/statie.yml']);

        parent::setUp();
    }

    public function testIdsAreKeys(): void
    {
        $generatorFilesByType = $this->generator->run();

        foreach ($generatorFilesByType as $generatorFiles) {
            foreach ($generatorFiles as $key => $generatorFile) {
                /** @var AbstractGeneratorFile $generatorFile */
                $this->assertSame($key, $generatorFile->getId());
            }
        }
    }

    public function testPosts(): void
    {
        $generatorFilesByType = $this->generator->run();
        $postFiles = $generatorFilesByType['posts'];

        $this->assertCount(6, $postFiles);

        $this->fileSystemWriter->renderFiles($postFiles);

        // posts
        $this->assertFileExists($this->outputDirectory . '/blog/2016/10/10/title/index.html');
        $this->assertFileExists($this->outputDirectory . '/blog/2016/01/02/second-title/index.html');

        $this->assertFileExists($this->outputDirectory . '/blog/2017/01/01/some-post/index.html');
        $this->assertFileExists($this->outputDirectory . '/blog/2017/01/05/another-related-post/index.html');
        $this->assertFileExists($this->outputDirectory . '/blog/2017/01/05/some-related-post/index.html');

        $this->assertFileExists($this->outputDirectory . '/blog/2017/02/05/offtopic-post/index.html');
    }

    public function testLectures(): void
    {
        $generatorFilesByType = $this->generator->run();
        $lectureFiles = $generatorFilesByType['lectures'];

        $this->assertCount(1, $lectureFiles);

        $this->fileSystemWriter->renderFiles($lectureFiles);

        // lectures
        $this->assertFileExists($this->outputDirectory . '/lecture/open-source-lecture/index.html');
    }

    public function testConfiguration(): void
    {
        $this->assertArrayNotHasKey('posts', $this->statieConfiguration->getOptions());
        $this->assertArrayNotHasKey('lectures', $this->statieConfiguration->getOptions());

        $this->generator->run();

        $this->assertArrayHasKey('posts', $this->statieConfiguration->getOptions());
        $this->assertArrayHasKey('lectures', $this->statieConfiguration->getOptions());

        $posts = $this->statieConfiguration->getOption('posts');
        $this->assertCount(6, $posts);

        $lectures = $this->statieConfiguration->getOption('lectures');
        $this->assertCount(1, $lectures);

        // detect date correctly from name
        $firstPost = array_pop($posts);
        $this->assertInstanceOf(DateTimeInterface::class, $firstPost['date']);
    }
}
