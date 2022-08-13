<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

final class Configuration
{
    /**
     * @param string[] $sources
     */
    public function __construct(
        private array $sources,
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

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }
}
