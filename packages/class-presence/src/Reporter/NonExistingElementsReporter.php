<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;

final class NonExistingElementsReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param string[][] $nonExistingClassesByFile
     * @param string[][] $nonExistingClassConstantsByFile
     */
    public function reportNonExistingElements(
        array $nonExistingClassesByFile,
        array $nonExistingClassConstantsByFile
    ): void {
        $i = 0;

        foreach ($nonExistingClassesByFile as $file => $nonExistingClasses) {
            $fileMssage = sprintf('<options=bold>%d) %s</>', ++$i, $file);
            $this->symfonyStyle->writeln($fileMssage);
            $this->symfonyStyle->newLine();

            foreach ($nonExistingClasses as $nonExistingClass) {
                $errorMessage = sprintf('Class "%s" not found', $nonExistingClass);
                $this->symfonyStyle->error($errorMessage);
            }
        }

        foreach ($nonExistingClassConstantsByFile as $file => $nonExistingClassConstants) {
            $fileMssage = sprintf('<options=bold>%d) %s</>', ++$i, $file);
            $this->symfonyStyle->writeln($fileMssage);

            foreach ($nonExistingClassConstants as $nonExistingClassConstant) {
                $errorMessage = sprintf('Class constant "%s" does not exist', $nonExistingClassConstant);
                $this->symfonyStyle->error($errorMessage);
            }
        }
    }
}
