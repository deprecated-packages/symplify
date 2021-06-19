<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFactory;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

abstract class AbstractCheckCommand extends AbstractSymplifyCommand
{
    protected EasyCodingStandardStyle $easyCodingStandardStyle;

    protected EasyCodingStandardApplication $easyCodingStandardApplication;

    protected LoadedCheckersGuard $loadedCheckersGuard;

    protected ConfigurationFactory $configurationFactory;

    #[Required]
    public function autowireAbstractCheckCommand(
        ConfigurationFactory $configurationFactory,
        EasyCodingStandardApplication $easyCodingStandardApplication,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        LoadedCheckersGuard $loadedCheckersGuard
    ): void {
        $this->configurationFactory = $configurationFactory;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->loadedCheckersGuard = $loadedCheckersGuard;
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::PATHS,
            // optional is on purpose here, since path from ecs.php can se ubsed
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The path(s) to be checked.'
        );

        $this->addOption(Option::FIX, null, null, 'Fix found violations.');

        $this->addOption(Option::CLEAR_CACHE, null, null, 'Clear cache for already checked files.');

        $this->addOption(
            Option::NO_PROGRESS_BAR,
            null,
            InputOption::VALUE_NONE,
            'Hide progress bar. Useful e.g. for nicer CI output.'
        );

        $this->addOption(
            Option::NO_ERROR_TABLE,
            null,
            InputOption::VALUE_NONE,
            'Hide error table. Useful e.g. for fast check of error count.'
        );

        $this->addOption(
            Option::OUTPUT_FORMAT,
            null,
            InputOption::VALUE_REQUIRED,
            'Select output format',
            ConsoleOutputFormatter::NAME
        );

        $this->addOption(
            Option::MATCH_GIT_DIFF,
            null,
            InputOption::VALUE_NONE,
            'Execute only on file(s) matching the git diff.'
        );
    }
}
