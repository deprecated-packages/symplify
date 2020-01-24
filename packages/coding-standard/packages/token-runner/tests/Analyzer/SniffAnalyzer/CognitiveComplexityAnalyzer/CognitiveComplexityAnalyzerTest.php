<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Tests\Analyzer\SniffAnalyzer\CognitiveComplexityAnalyzer;

use Iterator;
use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\CognitiveComplexityAnalyzer;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

final class CognitiveComplexityAnalyzerTest extends TestCase
{
    /**
     * @var CognitiveComplexityAnalyzer
     */
    private $cognitiveComplexityAnalyzer;

    protected function setUp(): void
    {
        $this->cognitiveComplexityAnalyzer = new CognitiveComplexityAnalyzer();
    }

    /**
     * @dataProvider provideTokensAndExpectedCognitiveComplexity()
     */
    public function test(string $filePath, int $expectedCognitiveComplexity): void
    {
        $fileContent = FileSystem::read($filePath);
        $tokens = $this->fileToTokens($fileContent);
        $functionTokenPosition = null;
        foreach ($tokens as $position => $token) {
            if ($token['code'] === T_FUNCTION) {
                $functionTokenPosition = $position;
                break;
            }
        }

        $cognitiveComplexity = $this->cognitiveComplexityAnalyzer->computeForFunctionFromTokensAndPosition(
            $tokens,
            $functionTokenPosition
        );

        $this->assertSame($expectedCognitiveComplexity, $cognitiveComplexity);
    }

    /**
     * Here are tested all examples from https://www.sonarsource.com/docs/CognitiveComplexity.pdf
     */
    public function provideTokensAndExpectedCognitiveComplexity(): Iterator
    {
        yield [__DIR__ . '/Source/function.php.inc', 9];
        yield [__DIR__ . '/Source/function2.php.inc', 6];
        yield [__DIR__ . '/Source/function3.php.inc', 1];
        yield [__DIR__ . '/Source/function4.php.inc', 2];
        yield [__DIR__ . '/Source/function5.php.inc', 19];
        yield [__DIR__ . '/Source/function6.php.inc', 0];
        yield [__DIR__ . '/Source/function7.php.inc', 3];
        yield [__DIR__ . '/Source/function8.php.inc', 7];
    }

    /**
     * @return mixed[]
     */
    private function fileToTokens(string $fileContent): array
    {
        return (new PHP($fileContent, $this->getLegacyConfig(), EolConfiguration::getEolChar()))->getTokens();
    }

    /**
     * @return Config|stdClass
     */
    private function getLegacyConfig()
    {
        $config = new stdClass();
        $config->tabWidth = 4;
        $config->annotations = false;
        $config->encoding = 'UTF-8';

        return $config;
    }
}
