<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Renderable\RenderableFilesProcessor;

final class RenderableFilesProcessorTest extends TestCase
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

        $this->configuration->setSourceDirectory(__DIR__ . '/RenderFilesProcessorSource/source');
        $this->configuration->setOutputDirectory(__DIR__ . '/RenderFilesProcessorSource/output');
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/RenderFilesProcessorSource/output');
    }

    public function test(): void
    {
        $finder = Finder::findFiles('*')->from(__DIR__ . '/RenderFilesProcessorSource/source')
            ->getIterator();
        $fileInfos = iterator_to_array($finder);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $this->assertFileExists(__DIR__ . '/RenderFilesProcessorSource/output/file/index.html');
        $this->assertFileEquals(
            __DIR__ . '/RenderFilesProcessorSource/file-expected.html',
            __DIR__ . '/RenderFilesProcessorSource/output/file/index.html'
        );
    }

    public function testAmp(): void
    {
        $finder = Finder::findFiles('*')->from(__DIR__ . '/RenderFilesProcessorSource/source')
            ->getIterator();
        $fileInfos = iterator_to_array($finder);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $htmlContactFile = __DIR__ . '/RenderFilesProcessorSource/output/contact/index.html';
        $ampContactFile = __DIR__ . '/RenderFilesProcessorSource/output/amp/contact/index.html';

        $this->assertFileExists($htmlContactFile);
        $this->assertFileExists($ampContactFile);

        $this->assertFileEquals(__DIR__ . '/RenderFilesProcessorSource/contact-expected.html', $htmlContactFile);
        $this->assertFileEquals(__DIR__ . '/RenderFilesProcessorSource/amp-contact-expected.html', $ampContactFile);
    }

    public function testPosts(): void
    {
        $finder = Finder::findFiles('*')->from(__DIR__ . '/RenderFilesProcessorSource/source/_posts')
            ->getIterator();
        $fileInfos = iterator_to_array($finder);

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
}
