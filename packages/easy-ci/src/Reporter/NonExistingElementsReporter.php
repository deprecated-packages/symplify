<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ValueObject\NonExistingClassesInFile;

final class NonExistingElementsReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    /**
     * @param NonExistingClassesInFile[] $nonExistingClassesByFiles
     * @param string[][] $nonExistingClassConstantsByFile
     */
    public function reportNonExistingElements(
        array $nonExistingClassesByFiles,
        array $nonExistingClassConstantsByFile
    ): void {
        $i = 0;

        foreach ($nonExistingClassesByFiles as $nonExistingClassesByFile) {
            $filePath = $nonExistingClassesByFile->getFilePath();

            $fileMessage = sprintf('<options=bold>%d) %s</>', ++$i, $filePath);
            $this->symfonyStyle->writeln($fileMessage);
            $this->symfonyStyle->newLine();

            foreach ($nonExistingClassesByFile->getNonExistingClasses() as $nonExistingClass) {
                $errorMessage = sprintf('Class "%s" not found', $nonExistingClass);
                $this->symfonyStyle->error($errorMessage);
            }
        }

        foreach ($nonExistingClassConstantsByFile as $file => $nonExistingClassConstants) {
            $fileMessage = sprintf('<options=bold>%d) %s</>', ++$i, $file);
            $this->symfonyStyle->writeln($fileMessage);

            foreach ($nonExistingClassConstants as $nonExistingClassConstant) {
                $errorMessage = sprintf('Class constant "%s" does not exist', $nonExistingClassConstant);
                $this->symfonyStyle->error($errorMessage);
            }
        }
    }
}
