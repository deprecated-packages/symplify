<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ForeachCommand extends Command
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    public function __construct(ComposerJsonProvider $composerJsonProvider)
    {
        parent::__construct();

        $this->composerJsonProvider = $composerJsonProvider;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Execute a given command for each package');
        $this->addArgument('cmd', InputArgument::REQUIRED, 'Command to execute for each package');
        $this->addArgument('args', InputArgument::IS_ARRAY, 'Optional arguments for the given command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdErrOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $command = array_merge([$input->getArgument('cmd')], $input->getArgument('args'));
        $fileInfos = $this->composerJsonProvider->getPackagesFileInfos();

        foreach ($fileInfos as $fileInfo) {
            $process = new Process($command, $fileInfo->getPath());

            $process->mustRun(static function ($type, $data) use ($output, $stdErrOutput): void {
                if ($type === Process::ERR) {
                    $stdErrOutput->write($data);
                } else {
                    $output->write($data);
                }
            });
        }

        return ShellCode::SUCCESS;
    }
}
