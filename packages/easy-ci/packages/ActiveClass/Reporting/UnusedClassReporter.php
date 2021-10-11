<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Reporting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;

final class UnusedClassReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    /**
     * @param FileWithClass[] $unusedFilesWithClasses
     * @param FileWithClass[] $existingFilesWithClasses
     */
    public function reportResult(array $unusedFilesWithClasses, array $existingFilesWithClasses): int
    {
        if ($unusedFilesWithClasses === []) {
            $successMessage = sprintf(
                'All the %d services are used. Great job!',
                count($existingFilesWithClasses),
            );
            $this->symfonyStyle->success($successMessage);
            return Command::SUCCESS;
        }

        foreach ($unusedFilesWithClasses as $unusedFileWithClass) {
            $this->symfonyStyle->writeln(' * ' . $unusedFileWithClass->getClassName());
            $this->symfonyStyle->writeln($unusedFileWithClass->getRelativeFilepath());
            $this->symfonyStyle->newLine();
        }

        $successMessage = sprintf(
            'Found %d unused classes. Check them, remove them or correct the command.',
            count($unusedFilesWithClasses)
        );

        $this->symfonyStyle->error($successMessage);

        return Command::FAILURE;
    }
}
