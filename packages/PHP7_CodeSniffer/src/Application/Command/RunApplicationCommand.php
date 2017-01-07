<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Application\Command;

final class RunApplicationCommand
{
    /**
     * @var array
     */
    private $source;

    /**
     * @var array
     */
    private $standards;

    /**
     * @var array
     */
    private $sniffs;

    /**
     * @var array
     */
    private $excludedSniffs;

    /**
     * @var bool
     */
    private $isFixer;

    public function __construct(
        array $source,
        array $standards,
        array $sniffs,
        array $excludedSniffs,
        bool $isFixer
    ) {
        $this->source = $source;
        $this->standards = $standards;
        $this->sniffs = $sniffs;
        $this->excludedSniffs = $excludedSniffs;
        $this->isFixer = $isFixer;
    }

    public function getSource() : array
    {
        return $this->source;
    }

    public function getStandards() : array
    {
        return $this->standards;
    }

    public function getSniffs() : array
    {
        return $this->sniffs;
    }

    public function getExcludedSniffs() : array
    {
        return $this->excludedSniffs;
    }

    public function isFixer() : bool
    {
        return $this->isFixer;
    }
}
