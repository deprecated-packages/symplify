<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Application\Command;

final class RunCommand
{
    /**
     * @var bool
     */
    private $runServer;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(bool $runServer, string $sourceDirectory, string $outputDirectory)
    {
        $this->runServer = $runServer;
        $this->sourceDirectory = $sourceDirectory;
        $this->outputDirectory = $outputDirectory;
    }

    public function isRunServer() : bool
    {
        return $this->runServer;
    }

    public function getOutputDirectory() : string
    {
        return $this->outputDirectory;
    }

    public function getSourceDirectory() : string
    {
        return $this->sourceDirectory;
    }
}
