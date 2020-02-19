<?php

declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Generator;

abstract class AbstractGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const SOURCE_DIRECTORY = __DIR__ . '/GeneratorSource/source';

    /**
     * @var string
     */
    protected $outputDirectory = __DIR__ . '/GeneratorSource/output';

    /**
     * @var StatieConfiguration
     */
    protected $statieConfiguration;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var FileSystemWriter
     */
    protected $fileSystemWriter;

    protected function setUp(): void
    {
        $this->generator = self::$container->get(Generator::class);
        $this->fileSystemWriter = self::$container->get(FileSystemWriter::class);

        $this->prepareConfiguration();
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory);
    }

    private function prepareConfiguration(): void
    {
        $this->statieConfiguration = self::$container->get(StatieConfiguration::class);
        $this->statieConfiguration->setSourceDirectory(self::SOURCE_DIRECTORY);
        $this->statieConfiguration->setOutputDirectory($this->outputDirectory);
    }
}
