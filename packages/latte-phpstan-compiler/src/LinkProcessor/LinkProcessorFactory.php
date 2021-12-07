<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\LinkProcessor;

use Symplify\LattePHPStanCompiler\Contract\LinkProcessorInterface;

final class LinkProcessorFactory
{
    /**
     * @param LinkProcessorInterface[] $linkProcessors
     */
    public function __construct(
        private array $linkProcessors
    ) {
    }

    public function create(string $targetName): ?LinkProcessorInterface
    {
        foreach ($this->linkProcessors as $linkProcessor) {
            if ($linkProcessor->check($targetName)) {
                return $linkProcessor;
            }
        }

        return null;
    }
}
