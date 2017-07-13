<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable;

use DateTimeInterface;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Throwable;

final class PostFilesProcessorTest extends TestCase
{
    /**
     * @var RenderableFilesProcessor
     */
    private $renderableFilesProcessor;

    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->createWithConfig(__DIR__ . '/RenderFilesProcessorSource/statie.neon');

        $this->renderableFilesProcessor = $container->get(RenderableFilesProcessor::class);
        $this->configuration = $container->get(Configuration::class);
        $this->configuration->setOutputDirectory(__DIR__ . '/RenderFilesProcessorSource/output');

        // add post layout
        /** @var DynamicStringLoader $dynamicStringLoader */
        $dynamicStringLoader = $container->get(DynamicStringLoader::class);
        $dynamicStringLoader->changeContent(
            'post',
            file_get_contents(__DIR__ . '/RenderFilesProcessorSource/_layouts/post.latte')
        );
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/RenderFilesProcessorSource/output');
    }

    public function testPosts(): void
    {
        $fileInfos = $this->findPostFiles();
        $this->assertCount(2, $fileInfos);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $normalPostLocation = __DIR__ . '/RenderFilesProcessorSource/output/blog/2016/01/02/second-title/index.html';
        $ampPostLocation = __DIR__ . '/RenderFilesProcessorSource/output/amp/blog/2016/01/02/second-title/index.html';
        $this->assertFileExists(__DIR__ . '/RenderFilesProcessorSource/output/blog/2016/10/10/title/index.html');
        $this->assertFileExists($normalPostLocation);
        $this->assertFileExists($ampPostLocation);

        $this->assertFalse(file_get_contents($normalPostLocation) === file_get_contents($ampPostLocation));

        $this->assertArrayHasKey('posts', $this->configuration->getOptions());
    }

    public function testPostWithLayoutContent(): void
    {
        $this->renderableFilesProcessor->processFiles($this->findPostFiles());

        $this->assertStringEqualsFile(
            __DIR__ . '/RenderFilesProcessorSource/post-with-latte-blocks-expected.html',
            file_get_contents(__DIR__ . '/RenderFilesProcessorSource/output/blog/2016/01/02/second-title/index.html')
        );
    }

    public function testPost(): void
    {
        $post = $this->getPost();

        $this->assertSame(9, $post->getWordCount());
        $this->assertSame(1, $post->getReadingTimeInMinutes());

        $this->assertFalse(isset($post['some_key']));
        $this->assertInstanceOf(DateTimeInterface::class, $post['date']);
    }

    public function testPostExceptionsOnUnset(): void
    {
        $post = $this->getPost();
        $this->expectException(Throwable::class);
        unset($post['key']);
    }

    public function testPostExceptionOnSet(): void
    {
        $post = $this->getPost();
        $this->expectException(Throwable::class);
        $post['key'] = 'value';
    }

    public function testPostExceptionOnGetNonExisting(): void
    {
        $post = $this->getPost();
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage(sprintf(
            'Value "key" was not found for "%s" object. Available values are "layout", "title", "relativeUrl"',
            PostFile::class)
        );
        $value = $post['key'];
    }

    /**
     * @return SplFileInfo[]
     */
    private function findPostFiles(): array
    {
        $finder = Finder::findFiles('*')->from(__DIR__ . '/RenderFilesProcessorSource/source/_posts')
            ->getIterator();

        return iterator_to_array($finder);
    }

    private function getPost(): PostFile
    {
        $this->renderableFilesProcessor->processFiles($this->findPostFiles());
        $posts = $this->configuration->getOptions()['posts'];

        return array_pop($posts);
    }
}
