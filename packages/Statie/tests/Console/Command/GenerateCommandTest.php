<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Console\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Console\StatieConsoleApplication;
use Symplify\Statie\Exception\Utils\MissingDirectoryException;
use Symplify\Statie\HttpKernel\StatieKernel;

final class GenerateCommandTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . '/GenerateCommandSource/output';

    /**
     * @var StatieConsoleApplication
     */
    private $statieConsoleApplication;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        $this->statieConsoleApplication = self::$container->get(StatieConsoleApplication::class);
        $this->statieConsoleApplication->setCatchExceptions(false);
        $this->statieConsoleApplication->setAutoExit(false);

        $symfonyStyle = self::$container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(addslashes($this->outputDirectory));
    }

    public function test(): void
    {
        $stringInput = ['generate', addslashes(__DIR__) . '/GenerateCommandSource/source', '--output', addslashes($this->outputDirectory)];
        $input = new StringInput(implode(' ', $stringInput));

        $this->statieConsoleApplication->run($input, new NullOutput());

        $this->assertFileExists(addslashes($this->outputDirectory) . '/index.html');
    }

    public function testException(): void
    {
        $stringInput = sprintf('generate %s', addslashes(__DIR__) . '/GenerateCommandSource/missing');
        $input = new StringInput($stringInput);

        $this->expectException(MissingDirectoryException::class);

        $this->statieConsoleApplication->run($input, new NullOutput());
    }
}
