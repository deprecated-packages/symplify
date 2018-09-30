<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Init\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use function Safe\getcwd;

final class InitCommand extends Command
{
    /**
     * @var string
     */
    private const OUTPUT = 'output';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(Filesystem $filesystem, SymfonyStyle $symfonyStyle)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Creates empty monorepo directory and composer.json structure.');
        $this->addArgument(self::OUTPUT, InputArgument::OPTIONAL, 'Directory to generate monorepo into.', getcwd());
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var string $output */
        $output = $input->getArgument(self::OUTPUT);

        $this->filesystem->mirror(__DIR__ . '/../../templates/monorepo', $output);

        $this->symfonyStyle->success('Congrats! Your first monorepo is here.');
        $this->symfonyStyle->note(
            'Now try the next step - merge composer.json files from packages to the root one: ' .
            PHP_EOL .
            '"vendor/bin/monorepo-builder merge"'
        );
    }
}
