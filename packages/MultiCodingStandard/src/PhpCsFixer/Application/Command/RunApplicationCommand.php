<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\PhpCsFixer\Application\Command;

final class RunApplicationCommand
{
    /**
     * @var array
     */
    private $source;

    /**
     * @var array
     */
    private $fixerLevels;

    /**
     * @var array
     */
    private $fixers;

    /**
     * @var array
     */
    private $excludeFixers;

    /**
     * @var bool
     */
    private $isFixer;

    public function __construct(array $source, array $fixerLevels, array $fixers, array $excludeFixers, bool $isFixer)
    {
        $this->source = $source;
        $this->fixerLevels = $fixerLevels;
        $this->fixers = $fixers;
        $this->excludeFixers = $excludeFixers;
        $this->isFixer = $isFixer;
    }

    public function getSource(): array
    {
        return $this->source;
    }

    public function getFixerLevels(): array
    {
        return $this->fixerLevels;
    }

    public function getFixers(): array
    {
        return $this->fixers;
    }

    public function getExcludeFixers(): array
    {
        return $this->excludeFixers;
    }

    public function isFixer(): bool
    {
        return $this->isFixer;
    }
}
