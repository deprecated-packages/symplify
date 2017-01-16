<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\PhpCsFixer\Factory;

use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Fixer\FixerInterface;

final class FixerFactory
{
    /**
     * @return FixerInterface[]
     */
    public function createFromLevelsFixersAndExcludedFixers(array $fixerLevels, array $fixers, array $excludedFixers)
    {
        $fixersFromLevels = $this->createFromLevelsAndExcludedFixers($fixerLevels, $excludedFixers);
        $standaloneFixers = $this->createFromFixers($fixers);

        return array_merge($fixersFromLevels, $standaloneFixers);
    }

    private function createFromLevelsAndExcludedFixers(array $fixerLevels, array $excludedFixers) : array
    {
        if (!count($fixerLevels)) {
            return [];
        }

        $fixers = [];
        foreach ($fixerLevels as $fixerLevel) {
            $excludedFixersAsString = $this->turnExcludedFixersToString($excludedFixers);
            $newFixers = $this->resolveFixersForLevelsAndFixers($fixerLevel, $excludedFixersAsString);

            $fixers = array_merge($fixers, $newFixers);
        }

        return $fixers;
    }

    private function createFromFixers(array $fixers) : array
    {
        if (!count($fixers)) {
            return [];
        }

        $fixersAsString = $this->turnFixersToString($fixers);

        return $this->resolveFixersForLevelsAndFixers('none', $fixersAsString);
    }

    /**
     * @return FixerInterface[]
     */
    private function resolveFixersForLevelsAndFixers(string $level, string $fixersAsString) : array
    {
        $config = new Config();
        $configurationResolver = new ConfigurationResolver($config, [
            'rules' => $this->combineSetAndFixersToRules($level, $fixersAsString)
        ], getcwd());

        return $configurationResolver->getFixers();
    }

    private function turnFixersToString(array $fixers) : string
    {
        return $this->implodeWithPresign($fixers);
    }

    private function turnExcludedFixersToString(array $excludedFixers) : string
    {
        return $this->implodeWithPresign($excludedFixers, '-');
    }

    private function implodeWithPresign(array $items, string $presign = '')
    {
        if (count($items)) {
            return $presign.implode(','.$presign, $items);
        }

        return '';
    }

    private function combineSetAndFixersToRules(string $level, string $fixersAsString) : string
    {
        $rules = '';
        if ($level && $level !== 'none')  {
            $rules .= '@' . strtoupper($level);
        }

        if ($fixersAsString) {
            if ($rules) {
                $rules .= ',';
            }
            $rules .= $fixersAsString;
        }

        return $rules;
    }
}
