<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Parser;

use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;

final class FileToTokensParserTest extends TestCase
{
    /**
     * @var FileToTokensParser
     */
    private $fileToTokensParser;

    protected function setUp()
    {
        $this->fileToTokensParser = new FileToTokensParser(new EolCharDetector());
    }

    public function testParseFromFilePath()
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath(
            __DIR__.'/FileToTokensParserSource/SimplePhpFile.php'
        );

        $this->assertCount(7, $tokens);
    }
}
