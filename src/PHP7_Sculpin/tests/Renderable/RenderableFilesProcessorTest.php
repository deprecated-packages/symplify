<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\DI\Container\ContainerFactory;
use Symplify\PHP7_Sculpin\Renderable\RenderableFilesProcessor;

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
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__.'/RenderFilesProcessorSource/config/config.neon'
        );
        $this->renderableFilesProcessor = $container->getByType(RenderableFilesProcessor::class);
        $this->configuration = $container->getByType(Configuration::class);
    }

    public function test()
    {
        $finder = Finder::find('*')->from(__DIR__.'/RenderFilesProcessorSource/source')->getIterator();
        $fileInfos = iterator_to_array($finder);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $this->assertFileExists(__DIR__.'/RenderFilesProcessorSource/output/file/index.html');
        $this->assertFileEquals(
            __DIR__.'/RenderFilesProcessorSource/file-expected.html',
            __DIR__.'/RenderFilesProcessorSource/output/file/index.html'
        );
    }

    public function testPosts()
    {
        // fails for unknown reason: https://ci.appveyor.com/project/TomasVotruba/symplify/build/1.0.19#L458
        if (getenv('APPVEYOR')) {
            return;
        }

        $finder = Finder::find('*')->from(__DIR__.'/RenderFilesProcessorSource/source/_posts')->getIterator();
        $fileInfos = iterator_to_array($finder);

        $this->renderableFilesProcessor->processFiles($fileInfos);

        $this->assertTrue(
            isset($this->configuration->getOptions()['posts'])
        );
    }

    protected function tearDown()
    {
        if (getenv('APPVEYOR')) { // AppVeyor doesn't have rights to delete
            return;
        }

        FileSystem::delete(__DIR__.'/RenderFilesProcessorSource/output');
    }
}
