<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Console\Output;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;

final class MissingTwigTemplatePathReporter
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
     * @param string[] $errorMessages
     */
    public function report(array $errorMessages): int
    {
        if ($errorMessages === []) {
            $this->symfonyStyle->success('All templates exists');
            return ShellCode::SUCCESS;
        }

        foreach ($errorMessages as $errorMessage) {
            $this->symfonyStyle->note($errorMessage);
        }

        $missingTemplatesMessage = sprintf('Found %d missing templates', count($errorMessages));
        $this->symfonyStyle->error($missingTemplatesMessage);

        return ShellCode::ERROR;
    }
}
