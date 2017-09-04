<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

final class ClassTokensAnalyzer
{
    /**
     * @var int
     */
    private $endBracketIndex;

    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    private function __construct(Tokens $tokens, int $startIndex)
    {
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);

        $startBracketIndex = $tokens->getNextTokenOfKind($startIndex, ['{']);
        $this->endBracketIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBracketIndex);
    }

    public static function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): self
    {
        return new self($tokens, $startIndex);
    }

    /**
     * @return mixed[]
     */
    public function getPropertiesAndConstants(): array
    {
        return $this->filterPropertyAndConstantTokens($this->tokensAnalyzer->getClassyElements());
    }

    public function getClassEnd(): int
    {
        return $this->endBracketIndex;
    }

    /**
     * @param mixed[] $classyElements
     * @return mixed[]
     */
    private function filterPropertyAndConstantTokens(array $classyElements): array
    {
        $propertyAndConstantTokens = [];

        foreach ($classyElements as $index => $classyToken) {
            if ($classyToken['type'] !== 'property' && $classyToken['type'] !== 'const') {
                continue;
            }

            $propertyAndConstantTokens[$index] = $classyToken;
        }

        return $propertyAndConstantTokens;
    }
}
