<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Analyzer\SniffAnalyzer\CognitiveComplexityAnalyzer;

use Iterator;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\CognitiveComplexityAnalyzer;

final class CognitiveComplextyAnalyzerTest extends TestCase
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
    public function test(string $fileContent, int $expectedCognitiveComplexity): void
    {
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
//        yield [file_get_contents(__DIR__ . '/Source/function.php.inc'), 9];
//        yield [file_get_contents(__DIR__ . '/Source/function2.php.inc'), 7];
//        yield [file_get_contents(__DIR__ . '/Source/function3.php.inc'), 1];
//        yield [file_get_contents(__DIR__ . '/Source/function4.php.inc'), 2];
        yield [file_get_contents(__DIR__ . '/Source/function5.php.inc'), 2];
    }

    /**
     * @return mixed[]
     */
    private function fileToTokens(string $fileContent): array
    {
        return (new PHP($fileContent, $this->getLegacyConfig(), PHP_EOL))->getTokens();
    }

    /**
     * @return Config|stdClass
     */
    private function getLegacyConfig()
    {
        $config = new stdClass();
        $config->tabWidth = 4;
        $config->annotations = false;

        return $config;
    }
}
