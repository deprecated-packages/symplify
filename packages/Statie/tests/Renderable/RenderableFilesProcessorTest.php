<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DI\Container\ContainerFactory;
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

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->renderableFilesProcessor = $container->getByType(RenderableFilesProcessor::class);
        $this->configuration = $container->getByType(Configuration::class);

        $this->configuration->setSourceDirectory(__DIR__ . '/RenderFilesProcessorSource/source');
        $this->configuration->setOutputDirectory(__DIR__ . '/RenderFilesProcessorSource/output');
    }

    public function test()
    {
        $finder = Finder::findFiles('*')->from(__DIR__ . '/RenderFilesProcessorSource/source')->getIterator();
        $fileInfos = iterator_to_array($finder);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $this->assertFileExists(__DIR__ . '/RenderFilesProcessorSource/output/file/index.html');
        $this->assertFileEquals(
            __DIR__ . '/RenderFilesProcessorSource/file-expected.html',
            __DIR__ . '/RenderFilesProcessorSource/output/file/index.html'
        );
    }

    public function testPosts()
    {
        $finder = Finder::findFiles('*')->from(__DIR__ . '/RenderFilesProcessorSource/source/_posts')->getIterator();
        $fileInfos = iterator_to_array($finder);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $this->assertTrue(
            isset($this->configuration->getGlobalVariables()['posts'])
        );
    }

    protected function tearDown()
    {
        if (getenv('APPVEYOR')) { // AppVeyor doesn't have rights to delete
            return;
        }

        FileSystem::delete(__DIR__ . '/RenderFilesProcessorSource/output');
    }
}
