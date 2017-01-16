<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\PhpCsFixer\Application;

use ArrayIterator;
use Symfony\CS\Config;
use Symfony\CS\Fixer;
use Symplify\MultiCodingStandard\PhpCsFixer\Application\Command\RunApplicationCommand;
use Symplify\MultiCodingStandard\PhpCsFixer\Factory\FixerFactory;
use Symplify\MultiCodingStandard\PhpCsFixer\Report\DiffDataCollector;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;

final class Application
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var FixerFactory
     */
    private $fixerSetFactory;
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var DiffDataCollector
     */
    private $diffDataCollector;

    public function __construct(
        Fixer $fixer,
        FixerFactory $fixerSetFactory,
        SourceFinder $sourceFinder,
        DiffDataCollector $diffDataCollector
    ) {
        $this->fixer = $fixer;
        $this->fixerSetFactory = $fixerSetFactory;
        $this->sourceFinder = $sourceFinder;
        $this->diffDataCollector = $diffDataCollector;
    }

    public function runCommand(RunApplicationCommand $command)
    {
        $this->registerFixersToFixer($command->getFixerLevels(), $command->getFixers(), $command->getExcludeFixers());

        $this->runForSource($command->getSource(), $command->isFixer());
    }

    private function registerFixersToFixer(array $fixerLevels, array $fixers, array $excludedFixers)
    {
        $fixers = $this->fixerSetFactory->createFromLevelsFixersAndExcludedFixers(
            $fixerLevels, $fixers, $excludedFixers
        );

        $this->fixer->registerCustomFixers($fixers);
    }

    private function runForSource(array $source, bool $isFixer)
    {
        $this->registerSourceToFixer($source);

        /** @var Config $config */
        $config = $this->fixer->getConfigs()[0];
        $config->fixers($this->fixer->getFixers());

        $changedDiffs = $this->fixer->fix($config, !$isFixer, !$isFixer);
        $this->diffDataCollector->setDiffs($changedDiffs);
    }

    private function registerSourceToFixer(array $source)
    {
        $files = $this->sourceFinder->find($source);

        $config = new Config();
        $config->finder(new ArrayIterator($files));
        $this->fixer->addConfig($config);
    }
}
