<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Routing;

use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class Router
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var string[]
     */
    private $foundClasses = [];

    public function __construct(SniffFinder $sniffFinder)
    {
        $this->sniffFinder = $sniffFinder;
    }

    public function getClassFromSniffCode(string $sniffCode) : string
    {
        $sniffCode = $this->normalizeToSniffClassCode($sniffCode);

        if (isset($this->foundClasses[$sniffCode])) {
            return $this->foundClasses[$sniffCode];
        }

        $sniffClasses = $this->sniffFinder->findAllSniffClasses();
        if (isset($sniffClasses[$sniffCode])) {
            return $sniffClasses[$sniffCode];
        }

        return '';
    }

    private function normalizeToSniffClassCode(string $sniffCode) : string
    {
        $parts = explode('.', $sniffCode);
        if (count($parts) === 4) {
            return $parts[0].'.'.$parts[1].'.'.$parts[2];
        }

        return $sniffCode;
    }
}
