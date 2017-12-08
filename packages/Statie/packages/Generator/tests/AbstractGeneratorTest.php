<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Generator\Generator;

abstract class AbstractGeneratorTest extends TestCase
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
     * @var Container
     */
    protected $container;

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
        $this->container = (new ContainerFactory())->create();

        $this->configuration = $this->container->get(Configuration::class);
        $this->configuration->setSourceDirectory($this->sourceDirectory);
        $this->configuration->setOutputDirectory($this->outputDirectory);

        $this->generator = $this->container->get(Generator::class);
        $this->fileSystemWriter = $this->container->get(FileSystemWriter::class);

        // add post layout
        /** @var DynamicStringLoader $dynamicStringLoader */
        $dynamicStringLoader = $this->container->get(DynamicStringLoader::class);
        $dynamicStringLoader->changeContent(
            'post',
            file_get_contents($this->sourceDirectory . '/_layouts/post.latte')
        );
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory);
    }
}
