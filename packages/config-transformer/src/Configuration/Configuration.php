<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\ConfigTransformer\ValueObject\Option;
use Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;

final class Configuration implements SymfonyVersionFeatureGuardInterface
{
    /**
     * @var string[]
     */
    private $source = [];

    private ?float $targetSymfonyVersion = null;

    private bool $isDryRun = false;

    public function populateFromInput(InputInterface $input): void
    {
        $this->source = (array) $input->getArgument(Option::SOURCES);
        $this->targetSymfonyVersion = floatval($input->getOption(Option::TARGET_SYMFONY_VERSION));
        $this->isDryRun = boolval($input->getOption(Option::DRY_RUN));
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->source;
    }

    public function isAtLeastSymfonyVersion(float $symfonyVersion): bool
    {
        return $this->targetSymfonyVersion >= $symfonyVersion;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    public function changeSymfonyVersion(float $symfonyVersion): void
    {
        $this->targetSymfonyVersion = $symfonyVersion;
    }

    /**
     * @return string[]
     */
    public function getInputSuffixes(): array
    {
        return [Format::YAML, Format::YML, Format::XML];
    }
}
