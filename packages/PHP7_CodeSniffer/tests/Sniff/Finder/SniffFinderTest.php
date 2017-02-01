<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffFinderTest extends TestCase
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    protected function setUp()
    {
        $this->sniffFinder = Instantiator::createSniffFinder();
    }

    public function testFindAllSniffs()
    {
        $allSniffs = $this->sniffFinder->findAllSniffClasses();
        $this->assertGreaterThan(250, $allSniffs);
    }

    public function testFindSniffsInDirectory()
    {
        $sniffs = $this->sniffFinder->findAllSniffClassesInDirectory();
        $this->assertCount(12, $sniffs);
    }
}
