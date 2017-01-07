<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;

final class ExcludedSniffDataCollectorTest extends TestCase
{
    public function testIsSniffClassExcluded()
    {
        $excludedSniffDataCollector = new ExcludedSniffDataCollector();

        $excludedSniffDataCollector->addExcludedSniff('Standard.Category.Name');
        $excludedSniffDataCollector->addExcludedSniffs(['AnotherStandard.Category.Name']);

        $this->assertTrue(
            $excludedSniffDataCollector->isSniffCodeExcluded('Standard.Category.Name')
        );
        $this->assertFalse(
            $excludedSniffDataCollector->isSniffCodeExcluded('Standard.Category.NonexistingName')
        );
    }
}
