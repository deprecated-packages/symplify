<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Standard\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\PHP7_CodeSniffer\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\StandardNotFoundException;

final class StandardFinder
{
    /**
     * @var string[]
     */
    private $rulesets = [];

    /**
     * @param string[] $names
     * @return string[]
     */
    public function getRulesetPathsForStandardNames(array $names) : array
    {
        $rulesetPaths = [];
        foreach ($names as $name) {
            $rulesetPaths[$name] = $this->getRulesetPathForStandardName($name);
        }

        return $rulesetPaths;
    }

    public function getRulesetPathForStandardName(string $standardName) : string
    {
        if (isset($this->getStandards()[$standardName])) {
            return $this->getStandards()[$standardName];
        }

        throw new StandardNotFoundException(
            sprintf(
                'Standard "%s" was not found. Found standards are: %s.',
                $standardName,
                implode($this->getRulesetNames(), ', ')
            )
        );
    }

    /**
     * @return string[]
     */
    public function getStandards() : array
    {
        if ($this->rulesets) {
            return $this->rulesets;
        }

        foreach ($this->findRulesetFiles() as $rulesetFile) {
            $rulesetXml = simplexml_load_file($rulesetFile);

            $rulesetName = (string) $rulesetXml['name'];
            $this->rulesets[$rulesetName] = $rulesetFile;
        }

        return $this->rulesets;
    }

    /**
     * @return string[]
     */
    private function findRulesetFiles() : array
    {
        $installedStandards = (new Finder())->files()
            ->in(VendorDirProvider::provide())
            ->name('ruleset.xml');

        return array_keys(iterator_to_array($installedStandards));
    }

    private function getRulesetNames() : array
    {
        return array_keys($this->getStandards());
    }
}
