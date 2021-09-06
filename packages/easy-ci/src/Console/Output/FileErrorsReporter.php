<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Console\Output;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;

final class FileErrorsReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    /**
     * @param FileErrorInterface[] $fileErrors
     */
    public function report(array $fileErrors): int
    {
        if ($fileErrors === []) {
            $this->symfonyStyle->success('No errors found');
            return Command::SUCCESS;
        }

        foreach ($fileErrors as $fileError) {
            $this->symfonyStyle->writeln($fileError->getRelativeFilePath());
            $this->symfonyStyle->writeln(' * ' . $fileError->getErrorMessage());
            $this->symfonyStyle->newLine();
        }

        $errorMassage = sprintf('%d errors found', count($fileErrors));
        $this->symfonyStyle->error($errorMassage);

        return Command::FAILURE;
    }
}
