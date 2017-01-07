<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SniffCodeToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffCodeToSniffsFactoryTest extends TestCase
{
    /**
     * @var SniffCodeToSniffsFactory
     */
    private $sniffCodeToSniffsFactory;

    protected function setUp()
    {
        $this->sniffCodeToSniffsFactory = new SniffCodeToSniffsFactory(
            Instantiator::createRouter(),
            Instantiator::createSingleSniffFactory()
        );
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->sniffCodeToSniffsFactory->isMatch('One.Two.Three'));
        $this->assertFalse($this->sniffCodeToSniffsFactory->isMatch('fail'));
    }

    public function testCreate()
    {
        $sniffs = $this->sniffCodeToSniffsFactory->create('PSR2.Classes.ClassDeclaration');

        $this->assertCount(1, $sniffs);
        $this->assertInstanceOf(Sniff::class, array_pop($sniffs));
    }
}
