<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SingleSniffFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\SniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SingleSniffFactoryTest extends TestCase
{
    /**
     * @var SingleSniffFactory
     */
    private $singleSniffFactory;

    protected function setUp()
    {
        $this->singleSniffFactory = Instantiator::createSingleSniffFactory();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Sniff\Naming\InvalidSniffClassException
     */
    public function testCreateInvalidClassName()
    {
        $this->singleSniffFactory->create('mmissing');
    }

    public function testCreate()
    {
        $sniff = $this->singleSniffFactory->create(ClassDeclarationSniff::class);
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniff);
    }

    public function testPropertiesAreChanged()
    {
        /** @var LineLengthSniff $lineLenghtSniff */
        $lineLenghtSniff = $this->singleSniffFactory->create(LineLengthSniff::class);
        $this->assertSame(80, $lineLenghtSniff->lineLimit);
        $this->assertSame(100, $lineLenghtSniff->absoluteLineLimit);

        $sniffPropertyValueDataCollector = Instantiator::createSniffPropertyValueDataCollector();
        $ruleXmlElement = new SimpleXMLElement('<rule ref="Generic.Files.LineLength">
            <properties>
                <property name="lineLimit" value="120"/>
                <property name="absoluteLineLimit" value="0"/>
            </properties>
        </rule>');
        $sniffPropertyValueDataCollector->collectFromRuleXmlElement($ruleXmlElement);

        $singleSniffFactoryWithValues = new SingleSniffFactory(
            new ExcludedSniffDataCollector(),
            $sniffPropertyValueDataCollector
        );

        /** @var LineLengthSniff $lineLenghtSniff */
        $lineLenghtSniff = $singleSniffFactoryWithValues->create(LineLengthSniff::class);
        $this->assertSame(120, $lineLenghtSniff->lineLimit);
        $this->assertSame(0, $lineLenghtSniff->absoluteLineLimit);
    }
}
