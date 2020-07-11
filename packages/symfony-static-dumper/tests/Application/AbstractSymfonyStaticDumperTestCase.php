<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\Application;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;
use Symplify\SymfonyStaticDumper\Tests\TestProject\HttpKernel\TestSymfonyStaticDumperKernel;

abstract class AbstractSymfonyStaticDumperTestCase extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    protected const EXPECTED_DIRECTORY = __DIR__ . '/../Fixture/expected';

    /**
     * @var string
     */
    protected const OUTPUT_DIRECTORY = __DIR__ . '/../temp/output';

    /**
     * @var SymfonyStaticDumperApplication
     */
    private $symfonyStaticDumperApplication;

    protected function setUp(): void
    {
        FileSystem::delete(self::OUTPUT_DIRECTORY);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(self::OUTPUT_DIRECTORY);
    }

    public function application(): SymfonyStaticDumperApplication
    {
        if ($this->symfonyStaticDumperApplication === null) {
            $this->bootApplication();
        }

        return $this->symfonyStaticDumperApplication;
    }

    private function bootApplication(): void
    {
        $this->bootKernel(TestSymfonyStaticDumperKernel::class);

        $this->symfonyStaticDumperApplication = self::$container->get(SymfonyStaticDumperApplication::class);

        // disable output in tests
        $symfonyStyle = self::$container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }
}
