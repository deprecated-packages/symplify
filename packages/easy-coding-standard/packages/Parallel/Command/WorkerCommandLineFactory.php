<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Parallel\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Command\WorkerCommand;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Parallel\Command\WorkerCommandLineFactoryTest
 */
final class WorkerCommandLineFactory
{
    public function __construct(
        private CheckCommand $checkCommand
    ) {
    }

    public function create(string $mainScript, ?string $projectConfigFile, InputInterface $input): string
    {
        $args = array_merge([PHP_BINARY, $mainScript], array_slice($_SERVER['argv'], 1));
        $processCommandArray = [];

        foreach ($args as $arg) {
            if ($arg === CommandNaming::classToName(CheckCommand::class)) {
                break;
            }

            $processCommandArray[] = escapeshellarg($arg);
        }

        $processCommandArray[] = CommandNaming::classToName(WorkerCommand::class);
        if ($projectConfigFile !== null) {
            $processCommandArray[] = '--' . Option::CONFIG;
            $processCommandArray[] = escapeshellarg($projectConfigFile);
        }

        $checkCommandOptionNames = $this->getCheckCommandOptionNames();

        foreach ($checkCommandOptionNames as $checkCommandOptionName) {
            if (! $input->hasOption($checkCommandOptionName)) {
                continue;
            }

            // force json output
            if ($checkCommandOptionName === Option::OUTPUT_FORMAT) {

                
                continue;
            }

            /** @var bool|string|null $optionValue */
            $optionValue = $input->getOption($checkCommandOptionName);
            if (is_bool($optionValue)) {
                if ($optionValue) {
                    $processCommandArray[] = sprintf('--%s', $checkCommandOptionName);
                }
                continue;
            }

            if ($optionValue === null) {
                continue;
            }

            $processCommandArray[] = '--' . $checkCommandOptionName;
            $processCommandArray[] = escapeshellarg($optionValue);
        }

        /** @var string[] $paths */
        $paths = $input->getArgument(Option::PATHS);
        foreach ($paths as $path) {
            $processCommandArray[] = escapeshellarg($path);
        }

        // set json output
        $processCommandArray[] = '--' . Option::OUTPUT_FORMAT;
        $processCommandArray[] = escapeshellarg(JsonOutputFormatter::NAME);

        return implode(' ', $processCommandArray);
    }

    /**
     * @return string[]
     */
    private function getCheckCommandOptionNames(): array
    {
        $inputDefinition = $this->checkCommand->getDefinition();

        $optionNames = [];
        foreach ($inputDefinition->getOptions() as $inputOption) {
            $optionNames[] = $inputOption->getName();
        }

        return $optionNames;
    }
}
