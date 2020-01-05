<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\ApiGenerator;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\HttpKernel\StatieKernel;

final class ApiGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var StatieApplication
     */
    private $statieApplication;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(StatieKernel::class, [__DIR__ . '/statie.yaml']);

        $this->statieApplication = self::$container->get(StatieApplication::class);

        $symfonyStyle = self::$container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/StatieApplicationSource/output');
    }

    public function testRun(): void
    {
        $this->statieApplication->run(
            __DIR__ . '/StatieApplicationSource/source',
            __DIR__ . '/StatieApplicationSource/output'
        );

        $booksJsonFile = __DIR__ . '/StatieApplicationSource/output/api/books.json';

        $this->assertFileExists($booksJsonFile);
        $this->assertFileEquals(__DIR__ . '/Source/expected-books.json', $booksJsonFile);
    }
}
