<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Sniff\Routing\Router;

final class SniffCodeToSniffsFactory implements SniffFactoryInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var SingleSniffFactory
     */
    private $singleSniffFactory;

    public function __construct(Router $router, SingleSniffFactory $singleSniffFactory)
    {
        $this->router = $router;
        $this->singleSniffFactory = $singleSniffFactory;
    }

    public function isMatch(string $reference) : bool
    {
        $partsCount = count(explode('.', $reference));
        if ($partsCount >= 3 && $partsCount <=4) {
            return true;
        }

        return false;
    }

    /**
     * @return Sniff[]
     */
    public function create(string $sniffCode) : array
    {
        $sniffClassName = $this->router->getClassFromSniffCode($sniffCode);
        $sniff = $this->singleSniffFactory->create($sniffClassName);
        if ($sniff !== null) {
            return [$sniff];
        }


        return [];
    }
}
