<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Console\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Console\Application;
use Symplify\Statie\Exception\Utils\MissingDirectoryException;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use function Safe\sprintf;

final class GenerateCommandTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . '/GenerateCommandSource/output';

    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = $this->container->get(Application::class);
        $this->application->setCatchExceptions(false);
        $this->application->setAutoExit(false);

        /** @var SymfonyStyle $symfonyStyle */
        $symfonyStyle = $this->container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory);
    }

    public function test(): void
    {
        $stringInput = ['generate', __DIR__ . '/GenerateCommandSource/source', '--output', $this->outputDirectory];
        $input = new StringInput(implode(' ', $stringInput));

        $this->application->run($input, new NullOutput());

        $this->assertFileExists($this->outputDirectory . '/index.html');
    }

    public function testException(): void
    {
        $stringInput = sprintf('generate %s', __DIR__ . '/GenerateCommandSource/missing');
        $input = new StringInput($stringInput);

        $this->expectException(MissingDirectoryException::class);

        $this->application->run($input, new NullOutput());
    }
}
