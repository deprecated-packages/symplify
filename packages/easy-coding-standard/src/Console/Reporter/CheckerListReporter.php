<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Reporter;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CheckerListReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    /**
     * @param FixerInterface[]|Sniff[] $checkers
     */
    public function report(array $checkers, string $type): void
    {
        if ($checkers === []) {
            return;
        }

        $checkerNames = array_map(fn ($checker): string => $checker::class, $checkers);

        $sectionMessage = sprintf('%d checker%s from %s:', count($checkers), count($checkers) === 1 ? '' : 's', $type);
        $this->symfonyStyle->section($sectionMessage);

        sort($checkerNames);
        $this->symfonyStyle->listing($checkerNames);
    }
}
