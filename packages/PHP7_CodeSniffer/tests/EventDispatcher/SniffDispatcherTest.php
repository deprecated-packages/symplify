<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Tests\EventDispatcher;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;

final class SniffDispatcherTest extends TestCase
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    protected function setUp()
    {
        $this->sniffDispatcher = new SniffDispatcher(
            new CurrentListenerSniffCodeProvider()
        );
    }

    public function testAddSniffListeners()
    {
        $sniffs = [new ClassDeclarationSniff()];
        $this->sniffDispatcher->addSniffListeners($sniffs);

        $this->assertCount(3, $this->sniffDispatcher->getListeners());
        $this->assertCount(1, $this->sniffDispatcher->getListeners(T_CLASS));
    }

    public function testDispatch()
    {
        $sniffs = [new ClassDeclarationSniff()];
        $this->sniffDispatcher->addSniffListeners($sniffs);

        $fileMock = $this->prophesize(File::class)
            ->reveal();

        $event = new CheckFileTokenEvent($fileMock, 5);
        $this->sniffDispatcher->dispatch(T_CLASS, $event);
    }
}
