<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\Application;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;
use Symplify\SymfonyStaticDumper\Routing\RoutesProvider;
use Symplify\SymfonyStaticDumper\Tests\TestProject\HttpKernel\TestSymfonyStaticDumperKernel;

final class SymfonyStaticDumperApplicationTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const EXPECTED_DIRECTORY = __DIR__ . '/../Fixture/expected';

    /**
     * @var string
     */
    private const OUTPUT_DIRECTORY = __DIR__ . '/../temp/output';

    /**
     * @var SymfonyStaticDumperApplication
     */
    private $symfonyStaticDumperApplication;

    /**
     * @var RoutesProvider
     */
    private $routesProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TestSymfonyStaticDumperKernel::class);

        $this->symfonyStaticDumperApplication = self::$container->get(SymfonyStaticDumperApplication::class);
        $this->routesProvider = self::$container->get(RoutesProvider::class);

        // disable output in tests
        $symfonyStyle = self::$container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(self::OUTPUT_DIRECTORY);
    }

    public function test(): void
    {
        $this->symfonyStaticDumperApplication->run(__DIR__ . '/../test_project/public', self::OUTPUT_DIRECTORY);

        // css
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/some.css');
        $this->assertFileEquals(self::EXPECTED_DIRECTORY . '/some.css', self::OUTPUT_DIRECTORY . '/some.css');

        // controllers
        $this->assertCount(4, $this->routesProvider->provide());

        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/kedlubna/index.html');
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/kedlubna/index.html',
            self::OUTPUT_DIRECTORY . '/kedlubna/index.html'
        );

        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/api.json');

        $expectedFileContent = FileSystem::read(self::EXPECTED_DIRECTORY . '/api.json');
        $expectedFileContent = trim($expectedFileContent);

        $outputFileContent = FileSystem::read(self::OUTPUT_DIRECTORY . '/api.json');
        $outputFileContent = trim($outputFileContent);

        $this->assertSame($expectedFileContent, $outputFileContent);

        // static page with TemplateController
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/static/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/static.html');
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/static.html',
            self::OUTPUT_DIRECTORY . '/static/index.html'
        );
        $this->assertFileEquals(
            self::OUTPUT_DIRECTORY . '/static.html',
            self::OUTPUT_DIRECTORY . '/static/index.html'
        );
    }
}
