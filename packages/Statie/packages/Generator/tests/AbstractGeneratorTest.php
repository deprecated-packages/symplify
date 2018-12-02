<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Nette\Utils\FileSystem;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Tests\AbstractConfigAwareContainerTestCase;

abstract class AbstractGeneratorTest extends AbstractConfigAwareContainerTestCase
{
    /**
     * @var string
     */
    protected $outputDirectory = __DIR__ . '/GeneratorSource/output';

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var FileSystemWriter
     */
    protected $fileSystemWriter;

    /**
     * @var string
     */
    private $sourceDirectory = __DIR__ . '/GeneratorSource/source';

    protected function setUp(): void
    {
        $this->generator = $this->container->get(Generator::class);
        $this->fileSystemWriter = $this->container->get(FileSystemWriter::class);

        $this->prepareConfiguration();
        $this->prepareLayouts();
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory);
    }

    private function prepareConfiguration(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
        $this->configuration->setSourceDirectory($this->sourceDirectory);
        $this->configuration->setOutputDirectory($this->outputDirectory);
    }

    /**
     * Emulate layout loading
     */
    private function prepareLayouts(): void
    {
        /** @var ArrayLoader $arrayLoader */
        $arrayLoader = $this->container->get(ArrayLoader::class);
        $arrayLoader->changeContent(
            '_layouts/post.latte',
            FileSystem::read($this->sourceDirectory . '/_layouts/post.latte')
        );
        $arrayLoader->changeContent(
            '_layouts/lecture.latte',
            FileSystem::read($this->sourceDirectory . '/_layouts/lecture.latte')
        );
    }
}
