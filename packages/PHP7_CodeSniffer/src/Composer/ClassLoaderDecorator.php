<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Composer;

use Composer\Autoload\ClassLoader;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class ClassLoaderDecorator
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    public function __construct(StandardFinder $standardFinder)
    {
        $this->standardFinder = $standardFinder;
    }

    public function decorate(ClassLoader $classLoader)
    {
        $standards = $this->standardFinder->getStandards();

        foreach ($standards as $stadardName => $standardRuleset) {
            if ($this->isDefaultStandard($stadardName)) {
                continue;
            }

            $standardNamespace = $this->detectStandardNamespaceFromStandardName($stadardName);
            $standardDir = dirname($standardRuleset);

            $classLoader->addPsr4(
                $standardNamespace . '\\',
                $standardDir . DIRECTORY_SEPARATOR . $standardNamespace
            );
        }
    }

    private function isDefaultStandard(string $stadardName) : bool
    {
        return in_array(
            $stadardName,
            ['PSR1', 'MySource', 'PSR2', 'Zend', 'PEAR', 'Squiz', 'Generic']
        );
    }

    private function detectStandardNamespaceFromStandardName(string $standardName) : string
    {
        return str_replace(' ', '', $standardName);
    }
}
