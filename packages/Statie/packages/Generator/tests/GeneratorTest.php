<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use DateTimeInterface;
use Nette\Utils\FileSystem;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Exception\Renderable\File\UnsupportedMethodException;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\File\PostFile;

final class GeneratorTest extends AbstractGeneratorTest
{
    public function testPosts(): void
    {
        $this->generator->run();

        $this->assertFileExists($this->outputDirectory. '/blog/2016/10/10/title/index.html');
        $this->assertFileExists($this->outputDirectory. '/blog/2016/01/02/second-title/index.html');

        $this->assertFileExists($this->outputDirectory. '/blog/2017/01/01/some-post/index.html');
        $this->assertFileExists($this->outputDirectory. '/blog/2017/01/05/another-related-post/index.html');
        $this->assertFileExists($this->outputDirectory. '/blog/2017/01/05/some-related-post/index.html');

        $this->assertFileExists($this->outputDirectory. '/blog/2017/02/05/offtopic-post/index.html');

        $this->assertStringEqualsFile(
            __DIR__ . '/GeneratorSource/expected/post-with-latte-blocks-expected.html',
            file_get_contents($this->outputDirectory. '/blog/2016/01/02/second-title/index.html')
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

    public function testPostExceptionsOnUnset(): void
    {
        $post = $this->getPost();
        $this->expectException(UnsupportedMethodException::class);
        unset($post['key']);
    }

    public function testPostExceptionOnSet(): void
    {
        $post = $this->getPost();
        $this->expectException(UnsupportedMethodException::class);
        $post['key'] = 'value';
    }

    public function testPostExceptionOnGetNonExistingSuggestion(): void
    {
        $post = $this->getPost();

        $this->expectException(AccessKeyNotAvailableException::class);
        $this->expectExceptionMessage(sprintf(
            'Value "tite" was not found for "%s" object. Did you mean "title"?',
            PostFile::class
        ));

        $value = $post['tite'];
    }

    public function testPostExceptionOnGetNonExistingAllKeys(): void
    {
        $post = $this->getPost();

        $this->expectException(AccessKeyNotAvailableException::class);
        $this->expectExceptionMessage(sprintf(
            'Value "key" was not found for "%s" object. Available keys are: "id", "title", "relativeUrl".',
            PostFile::class
        ));

        $value = $post['key'];
    }

    private function getPost(int $order = 0): PostFile
    {
        $this->generator->run();

        $posts = $this->configuration->getOption('posts');

        return $posts[$order];
    }
}
