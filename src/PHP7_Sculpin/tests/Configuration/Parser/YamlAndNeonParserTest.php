<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Tests\Configuration\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;

final class YamlAndNeonParserTest extends TestCase
{
    public function test()
    {
        $yamlAndNeonParser = new YamlAndNeonParser();

        $neonConfig = $yamlAndNeonParser->decode(file_get_contents(__DIR__.'/YamlAndNeonParserSource/config.neon'));
        $this->assertSame($neonConfig, [
            'multiline' => 'one'.PHP_EOL.'two'.PHP_EOL.'three',
        ]);

        $yamlConfig = $yamlAndNeonParser->decode(file_get_contents(__DIR__.'/YamlAndNeonParserSource/config.yaml'));
        $this->assertSame($yamlConfig, [
            'multiline' => 'one two three'.PHP_EOL,
        ]);
    }
}
