<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffFinderTest extends TestCase
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    protected function setUp()
    {
        $this->sniffFinder = Instantiator::createSniffFinder();
        $this->standardFinder = new StandardFinder();
    }

    public function testFindAllSniffs()
    {
        $allSniffs = $this->sniffFinder->findAllSniffClasses();
        $this->assertGreaterThan(250, $allSniffs);
    }

    public function testFindSniffsInDirectory()
    {
        $psr2RulesetPath = $this->standardFinder->getRulesetPathForStandardName('PSR2');

        $sniffs = $this->sniffFinder->findAllSniffClassesInDirectory(
            dirname($psr2RulesetPath)
        );
        $this->assertCount(12, $sniffs);
    }
}
