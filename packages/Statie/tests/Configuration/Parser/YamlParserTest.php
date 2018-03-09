<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration\Parser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symplify\Statie\Configuration\Parser\YamlParser;

final class YamlParserTest extends TestCase
{
    /**
     * @var YamlParser
     */
    private $yamlParser;

    protected function setUp(): void
    {
        $this->yamlParser = new YamlParser();
    }

    public function testDecode(): void
    {
        $decodedYaml = $this->yamlParser->decode(file_get_contents(__DIR__ . '/YamlParserSource/config.yml'));
        $this->assertContains('one', $decodedYaml['multiline']);
        $this->assertContains('two', $decodedYaml['multiline']);

        $decodedYamlFromFile = $this->yamlParser->decodeFile(__DIR__ . '/YamlParserSource/config.yml');
        $this->assertSame($decodedYamlFromFile, $decodedYaml);
    }

    public function testErrorInDecodeFromFile(): void
    {
        $brokenYamlFilePath = __DIR__ . '/YamlParserSource/broken-config.yml';

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(sprintf(
            'A colon cannot be used in an unquoted mapping value in "%s" at line 2 (near " another_key: value").',
            $brokenYamlFilePath
        ));

        $this->yamlParser->decodeFile(__DIR__ . '/YamlParserSource/broken-config.yml');
    }
}
