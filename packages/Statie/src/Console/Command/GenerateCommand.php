<?php declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Statie\Application\StatieApplication;

final class GenerateCommand extends Command
{
    /**
     * @var StatieApplication
     */
    private $statieApplication;

    public function __construct(StatieApplication $statieApplicationRunner)
    {
        $this->statieApplication = $statieApplicationRunner;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate a site from source.');

        $this->addArgument('source', InputArgument::REQUIRED, 'Directory to load page FROM.');
        $this->addOption(
            'output',
            null,
            InputOption::VALUE_REQUIRED,
            'Directory to generate page TO.',
            getcwd() . DIRECTORY_SEPARATOR . 'output'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->statieApplication->run(
            $input->getArgument('source'),
            $input->getOption('output')
        );

        $output->writeln('<info>Website was successfully generated.</info>');

        return 0;
    }
}
