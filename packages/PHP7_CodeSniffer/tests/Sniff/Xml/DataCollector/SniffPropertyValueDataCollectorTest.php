<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Xml\DataCollector;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\SniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor\SniffPropertyValuesExtractor;

final class SniffPropertyValueDataCollectorTest extends TestCase
{
    /**
     * @var SniffPropertyValueDataCollector
     */
    private $sniffPropertyValueDataCollector;

    protected function setUp()
    {
        $this->sniffPropertyValueDataCollector = new SniffPropertyValueDataCollector(
            new SniffPropertyValuesExtractor()
        );
    }

    public function testCollectForRuleXmlElement()
    {
        $xmlElement = simplexml_load_file(__DIR__ . '/SniffPropertyValueDataCollectorSource/source.xml');
        $this->sniffPropertyValueDataCollector->collectFromRuleXmlElement($xmlElement);

        $sniff = new LineLengthSniff();
        $this->assertSame([
            'integer' => 5,
            'string' => 'hello',
            'bool' => false,
            'another_bool' => true,
            'caps_bool' => false,
            'caps_another_bool' => true,
            'array_with_keys' => [
                'key' => 'value',
                'anotherKey' => 'anotherValue'
            ],
            'simple_array' => [
                'value', 'anotherValue'
            ],
        ], $this->sniffPropertyValueDataCollector->getForSniff($sniff));
    }
}
