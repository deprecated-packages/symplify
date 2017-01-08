<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Parser\NeonParser;

final class NeonParserTest extends TestCase
{
    public function test()
    {
        $yamlAndNeonParser = new NeonParser();

        $neonConfig = $yamlAndNeonParser->decode(file_get_contents(__DIR__ . '/NeonParserSource/config.neon'));
        if ($this->isWindows()) {
            $this->assertContains('one', $neonConfig['multiline']);
            $this->assertContains('two', $neonConfig['multiline']);
        } else {
            $this->assertContains('one' . PHP_EOL . 'two', $neonConfig['multiline']);
        }
    }

    private function isWindows() : bool
    {
        return '\\' === DIRECTORY_SEPARATOR;
    }
}
