<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use DateTimeInterface;
use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Exception\Renderable\File\UnsupportedMethodException;
use Symplify\Statie\Renderable\File\PostFile;

final class GeneratorTest extends AbstractGeneratorTest
{
    public function testPosts(): void
    {
        $this->generator->run();

        $this->assertFileExists($this->outputDirectory . '/blog/2016/10/10/title/index.html');
        $this->assertFileExists($this->outputDirectory . '/blog/2016/01/02/second-title/index.html');

        $this->assertFileExists($this->outputDirectory . '/blog/2017/01/01/some-post/index.html');
        $this->assertFileExists($this->outputDirectory . '/blog/2017/01/05/another-related-post/index.html');
        $this->assertFileExists($this->outputDirectory . '/blog/2017/01/05/some-related-post/index.html');

        $this->assertFileExists($this->outputDirectory . '/blog/2017/02/05/offtopic-post/index.html');

        $this->assertStringEqualsFile(
            __DIR__ . '/GeneratorSource/expected/post-with-latte-blocks-expected.html',
            file_get_contents($this->outputDirectory . '/blog/2016/01/02/second-title/index.html')
        );
    }

    public function testConfiguration(): void
    {
        $this->assertArrayNotHasKey('posts', $this->configuration->getOptions());

        $this->generator->run();
        $this->assertArrayHasKey('posts', $this->configuration->getOptions());

        $posts = $this->configuration->getOption('posts');
        $this->assertCount(6, $posts);

        // detect date correctly from name
        $firstPost = $posts[0];
        $this->assertInstanceOf(DateTimeInterface::class, $firstPost['date']);
    }
}
