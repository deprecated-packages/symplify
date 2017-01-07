<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffFactoryTest extends TestCase
{
    /**
     * @dataProvider provideDataForResolver()
     */
    public function testResolveFromStandardsAndSniffs(
        array $standards,
        array $extraSniffs,
        array $excludedSniffs,
        int $sniffCount
    ) {
        $excludedSniffDataCollector = new ExcludedSniffDataCollector();
        $excludedSniffDataCollector->addExcludedSniffs($excludedSniffs);
        $sniffSetFactory = Instantiator::createSniffSetFactoryWithExcludedDataCollector(
            $excludedSniffDataCollector
        );

        $sniffs = $sniffSetFactory->createFromStandardsAndSniffs($standards, $extraSniffs);
        $this->assertCount($sniffCount, $sniffs);
        foreach ($sniffs as $sniff) {
            $this->assertNotNull($sniff, 'Null present in sniffs array');
        }
    }

    public function provideDataForResolver() : array
    {
        return [
            [
                [], [], [], 0
            ], [
                ['PSR2'], [], [], 48
            ], [
                ['PSR2'], ['PEAR.Commenting.ClassComment'], [], 49
            ], [
                ['PSR2'], [], ['PSR2.Namespaces.UseDeclaration'], 48
            ], [
                ['PSR2'],
                ['PEAR.Commenting.ClassComment'],
                ['PEAR.Commenting.ClassComment', 'PSR2.Namespaces.UseDeclaration'],
                48
            ],
        ];
    }
}
