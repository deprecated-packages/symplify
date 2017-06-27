<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Exception\Neon\InvalidNeonSyntaxException;

final class NeonParserTest extends TestCase
{
    /**
     * @var NeonParser
     */
    private $neonParser;

    protected function setUp(): void
    {
        $this->neonParser = new NeonParser;
    }

    public function testDecode(): void
    {
        $decodedNeon = $this->neonParser->decode(file_get_contents(__DIR__ . '/NeonParserSource/config.neon'));
        $this->assertContains('one', $decodedNeon['multiline']);
        $this->assertContains('two', $decodedNeon['multiline']);

        $decodedNeonFromFile = $this->neonParser->decodeFromFile(__DIR__ . '/NeonParserSource/config.neon');
        $this->assertSame($decodedNeonFromFile, $decodedNeon);
    }

    public function testErrorInDecodeFromFile(): void
    {
        $brokenNeonFilePath = __DIR__ . '/NeonParserSource/broken-config.neon';

        $this->expectException(InvalidNeonSyntaxException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid NEON syntax found in "%s" file: Bad indentation on line 2, column 2.', $brokenNeonFilePath
        ));

        $this->neonParser->decodeFromFile(__DIR__ . '/NeonParserSource/broken-config.neon');
    }
}
