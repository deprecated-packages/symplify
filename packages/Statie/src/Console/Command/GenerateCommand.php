<?php declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Application\StatieApplication;
use function Safe\getcwd;
use function Safe\sprintf;

final class GenerateCommand extends Command
{
    /**
     * @var string
     */
    private const OPTION_OUTPUT = 'output';

    /**
     * @var string
     */
    private const OPTION_SOURCE = 'source';

    /**
     * @var StatieApplication
     */
    private $statieApplication;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(StatieApplication $statieApplication, SymfonyStyle $symfonyStyle)
    {
        $this->statieApplication = $statieApplication;

        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate a site from source');

        $this->addArgument(self::OPTION_SOURCE, InputArgument::REQUIRED, 'Directory to load page FROM.');
        $this->addOption(
            self::OPTION_OUTPUT,
            null,
            InputOption::VALUE_REQUIRED,
            'Directory to generate page TO.',
            getcwd() . DIRECTORY_SEPARATOR . self::OPTION_OUTPUT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(self::OPTION_SOURCE);
        $output = (string) $input->getOption(self::OPTION_OUTPUT);

        $this->statieApplication->run($source, $output);

        $this->symfonyStyle->success(
            sprintf('Web was generated from "%s" source to "%s" output', $source, $output)
        );

        return ShellCode::SUCCESS;
    }
}
