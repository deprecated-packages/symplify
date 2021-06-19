<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ConfigurationFactory
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * Needs to run in the start of the life cycle, since the rest of workflow uses it.
     */
    public function createFromInput(InputInterface $input): Configuration
    {
        $sources = $this->resolvePaths($input);

        $isFixer = (bool) $input->getOption(Option::FIX);
        $shouldClearCache = (bool) $input->getOption(Option::CLEAR_CACHE);
        $showProgressBar = $this->canShowProgressBar($input);
        $showErrorTable = ! (bool) $input->getOption(Option::NO_ERROR_TABLE);
        $doesMatchGitDiff = (bool) $input->getOption(Option::MATCH_GIT_DIFF);

        $outputFormat = (string) $input->getOption(Option::OUTPUT_FORMAT);

        return new Configuration(
            $isFixer,
            $shouldClearCache,
            $showProgressBar,
            $showErrorTable,
            $sources,
            $outputFormat,
            $doesMatchGitDiff,
        );
    }

    private function canShowProgressBar(InputInterface $input): bool
    {
        $notJsonOutput = $input->getOption(Option::OUTPUT_FORMAT) !== JsonOutputFormatter::NAME;
        if (! $notJsonOutput) {
            return false;
        }

        return ! (bool) $input->getOption(Option::NO_PROGRESS_BAR);
    }

    /**
     * @param string[] $sources
     */
    private function ensureSourcesExists(array $sources): void
    {
        foreach ($sources as $source) {
            if (file_exists($source)) {
                continue;
            }

            throw new SourceNotFoundException(sprintf('Source "%s" does not exist.', $source));
        }
    }

    /**
     * @return string[]
     */
    private function resolvePaths(InputInterface $input): array
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument(Option::PATHS);
        if ($paths !== []) {
            $sources = $paths;
        } else {
            // if not paths are provided from CLI, use the config ones
            $sources = $this->parameterProvider->provideArrayParameter(Option::PATHS);
        }

        $this->ensureSourcesExists($sources);
        return $this->normalizeSources($sources);
    }

    /**
     * @param string[] $sources
     * @return string[]
     */
    private function normalizeSources(array $sources): array
    {
        foreach ($sources as $key => $value) {
            $sources[$key] = rtrim($value, DIRECTORY_SEPARATOR);
        }

        return $sources;
    }
}
