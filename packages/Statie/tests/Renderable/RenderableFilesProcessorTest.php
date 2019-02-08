<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable;

use Nette\Utils\FileSystem;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class RenderableFilesProcessorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . '/RenderFilesProcessorSource/output';

    /**
     * @var string
     */
    private $sourceDirectory = __DIR__ . '/RenderFilesProcessorSource/source';

    /**
     * @var RenderableFilesProcessor
     */
    private $renderableFilesProcessor;

    /**
     * @var FileFinder
     */
    private $fileFinder;

    protected function setUp(): void
    {
        $this->renderableFilesProcessor = $this->container->get(RenderableFilesProcessor::class);
        $this->fileFinder = $this->container->get(FileFinder::class);

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory($this->sourceDirectory);
        $configuration->setOutputDirectory($this->outputDirectory);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/RenderFilesProcessorSource/output');
    }

    public function test(): void
    {
        $fileInfos = $this->fileFinder->findRestOfRenderableFiles($this->sourceDirectory);
        $files = $this->renderableFilesProcessor->processFileInfos($fileInfos);

        $this->assertCount(1, $files);

        $contactFile = array_pop($files);
        $this->assertStringEqualsFile(
            __DIR__ . '/RenderFilesProcessorSource/contact-expected.html',
            $contactFile->getContent()
        );
    }
}
