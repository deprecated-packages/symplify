<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Neon\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Neon\Application\NeonFilesProcessor;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class CheckNeonCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private NeonFilesProcessor $neonFilesProcessor
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
        $this->setDescription('Analyze NEON files for complex syntax');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $neonFileInfos = $this->smartFinder->find($sources, '*.neon');

        $message = sprintf('Analysing %d *.neon files', count($neonFileInfos));
        $this->symfonyStyle->note($message);

        $fileErrors = $this->neonFilesProcessor->analyzeFileInfos($neonFileInfos);
        if ($fileErrors === []) {
            $this->symfonyStyle->success('No errors found');
            return self::SUCCESS;
        }

        foreach ($fileErrors as $fileError) {
            $this->symfonyStyle->writeln($fileError->getRelativeFilePath());
            $this->symfonyStyle->writeln(' * ' . $fileError->getErrorMessage());
            $this->symfonyStyle->newLine();
        }

        $errorMassage = sprintf('%d errors found', count($fileErrors));
        $this->symfonyStyle->error($errorMassage);

        return self::FAILURE;
    }
}
