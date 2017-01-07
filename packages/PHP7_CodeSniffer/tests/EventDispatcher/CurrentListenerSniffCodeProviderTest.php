<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Tests\EventDispatcher;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;

final class CurrentListenerSniffCodeProviderTest extends TestCase
{
    /**
     * @var CurrentListenerSniffCodeProvider
     */
    private $currentListenerSniffCodeProvider;

    protected function setUp()
    {
        $this->currentListenerSniffCodeProvider = new CurrentListenerSniffCodeProvider();
    }

    public function testGetCurrentListenerSniffCodeForEmpty()
    {
        $this->assertSame(
            '',
            $this->currentListenerSniffCodeProvider->getCurrentListenerSniffCode()
        );
    }

    public function testGetCurrentListenerSniffCodeForArray()
    {
        $this->currentListenerSniffCodeProvider->setCurrentListener(['someListener', 'someMethod']);
        $this->assertSame(
            '',
            $this->currentListenerSniffCodeProvider->getCurrentListenerSniffCode()
        );
    }

    public function testGetCurrentListenerSniffCodeForCallable()
    {
        $sniffObject = new ClassDeclarationSniff();

        $this->currentListenerSniffCodeProvider->setCurrentListener(function () use ($sniffObject) {
        });

        $this->assertSame(
            'PSR2.Classes.ClassDeclaration',
            $this->currentListenerSniffCodeProvider->getCurrentListenerSniffCode()
        );
    }
    public function testGetCurrentListenerSniffCodeForCallableNotSniff()
    {
        $this->currentListenerSniffCodeProvider->setCurrentListener(function () {
        });

        $this->assertEmpty($this->currentListenerSniffCodeProvider->getCurrentListenerSniffCode());
    }
}
