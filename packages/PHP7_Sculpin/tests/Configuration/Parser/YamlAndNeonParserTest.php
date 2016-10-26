<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Configuration\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;

final class YamlAndNeonParserTest extends TestCase
{
    public function test()
    {
        $yamlAndNeonParser = new YamlAndNeonParser();

        $neonConfig = $yamlAndNeonParser->decode(file_get_contents(__DIR__ . '/YamlAndNeonParserSource/config.neon'));
        if ($this->isWindows()) {
            $this->assertContains('one', $neonConfig['multiline']);
            $this->assertContains('two', $neonConfig['multiline']);
        } else {
            $this->assertContains('one' . PHP_EOL . 'two', $neonConfig['multiline']);
        }

        $yamlConfig = $yamlAndNeonParser->decode(file_get_contents(__DIR__ . '/YamlAndNeonParserSource/config.yaml'));
        $this->assertContains('one two', $yamlConfig['multiline']);
    }

    private function isWindows() : bool
    {
        return '\\' === DIRECTORY_SEPARATOR;
    }
}
