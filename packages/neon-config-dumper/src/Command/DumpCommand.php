<?php

declare(strict_types=1);

namespace Symplify\NeonConfigDumper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\NeonConfigDumper\Application\NeonConfigDumper;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SmartFileSystem\SmartFileSystem;

final class DumpCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

    /**
     * @var string
     */
    private const OPTION_OUTPUT_FILE = 'output-file';

    public function __construct(
        private NeonConfigDumper $neonConfigDumper,
        private SymfonyStyle $symfonyStyle,
        private SmartFileSystem $smartFileSystem,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Dump services from specific directory to specific service file');

        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Path to directory to look for services');
        $this->addOption(self::OPTION_OUTPUT_FILE, null, InputOption::VALUE_REQUIRED, 'Path to services output file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        $outputFile = (string) $input->getOption(self::OPTION_OUTPUT_FILE);

        $neonConfigContent = $this->neonConfigDumper->generate($source);
        if ($neonConfigContent === null) {
            $this->symfonyStyle->warning('Nothing to dump');
            return self::SUCCESS;
        }

        // 4. dump the file contents to target file
        $this->smartFileSystem->dumpFile($outputFile, $neonConfigContent);

        $successMessage = sprintf('File "%s" generated successfully', $outputFile);
        $this->symfonyStyle->success($successMessage);

        return self::SUCCESS;
    }
}
