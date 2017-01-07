<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Xml\Extractor;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor\SniffPropertyValuesExtractor;

final class SniffPropertyValuesExtractorTest extends TestCase
{
    /**
     * @var SniffPropertyValuesExtractor
     */
    private $sniffPropertyValuesExtractor;

    protected function setUp()
    {
        $this->sniffPropertyValuesExtractor = new SniffPropertyValuesExtractor();
    }

    /**
     * @dataProvider provideDataForExtractFromRuleXmlElement()
     */
    public function testProcess(string $elementData, array $expectedCustomPropertyValues)
    {
        $rule = new SimpleXMLElement($elementData);
        $ruleset = $this->sniffPropertyValuesExtractor->extractFromRuleXmlElement($rule);
        $this->assertSame($expectedCustomPropertyValues, $ruleset);
    }

    public function provideDataForExtractFromRuleXmlElement() : array
    {
        return [
            ['<rule ref="PSR1"/>', []],
            [
                '<rule ref="Generic.Files.LineEndings"> 
                    <properties>
                        <property name="eolChar" value="\n"/>
                    </properties>
                </rule>', [
                    'eolChar' => '\n'
                ]
            ],
            [
                '<rule ref="Generic.WhiteSpace.ScopeIndent"> 
                    <properties>
                        <property name="ignoreIndentationTokens"
                            type="array" value="T_COMMENT,T_DOC_COMMENT_OPEN_TAG"/>
                    </properties>
                </rule>', [
                    'ignoreIndentationTokens' => [
                        0 => 'T_COMMENT',
                        1 => 'T_DOC_COMMENT_OPEN_TAG'
                    ]
                ]
            ]
        ];
    }
}
