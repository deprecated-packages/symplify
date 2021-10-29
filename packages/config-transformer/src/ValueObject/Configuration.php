<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

use Symplify\ConfigTransformer\Enum\Format;

final class Configuration
{
    /**
     * @param string[] $sources
     */
    public function __construct(
        private array $sources,
        private float $targetSymfonyVersion,
        private bool $isDryRun
    ) {
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function isAtLeastSymfonyVersion(float $symfonyVersion): bool
    {
        return $this->targetSymfonyVersion >= $symfonyVersion;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    /**
     * @return string[]
     */
    public function getInputSuffixes(): array
    {
        return [Format::YAML, Format::YML, Format::XML];
    }
}
