<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('fix');
        $this->setDescription('Fix coding standard in particular directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getArgument('path') as $path) {
            $this->executeFixersForDirectory($path);
        }
        $this->io->success('Your code was successfully fixed!');

        return self::EXIT_CODE_SUCCESS;
    }

    private function executeFixersForDirectory(string $directory)
    {
        foreach ($this->runnerCollection->getRunners() as $runner) {
            $headline = 'Running ' . $this->getRunnerName($runner) . ' in ' . $directory;
            $this->io->section($headline);

            $this->io->text($runner->fixDirectory($directory));
        }
    }
}
