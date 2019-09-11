<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration\Parser;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symplify\Statie\Configuration\Parser\YamlParser;

final class YamlParserTest extends TestCase
{
    /**
     * @var YamlParser
     */
    private $yamlParser;

    protected function setUp(): void
    {
        $this->yamlParser = new YamlParser(new Parser());
    }

    public function testDecode(): void
    {
        $decodedYaml = $this->yamlParser->decode(FileSystem::read(__DIR__ . '/YamlParserSource/config.yml'));
        $this->assertStringContainsString('one', $decodedYaml['multiline']);
        $this->assertStringContainsString('two', $decodedYaml['multiline']);

        $content = FileSystem::read(__DIR__ . '/YamlParserSource/config.yml');

        $decodedYamlFromFile = $this->yamlParser->decode($content);
        $this->assertSame($decodedYamlFromFile, $decodedYaml);
    }

    public function testErrorInDecodeFromFile(): void
    {
        $brokenYamlFilePath = __DIR__ . '/YamlParserSource/broken-config.yml';

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(
            'A colon cannot be used in an unquoted mapping value at line 2 (near " another_key: value").'
        );

        $content = FileSystem::read($brokenYamlFilePath);
        $this->yamlParser->decode($content);
    }
}
