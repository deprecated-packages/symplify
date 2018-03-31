<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Exception\MissingImplementationException;
use Throwable;

final class BlockStartAndEndFinder
{
    /**
     * @var int[]
     */
    private $contentToBlockType = [
        '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        ')' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        '[' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        ']' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        '{' => Tokens::BLOCK_TYPE_CURLY_BRACE,
        '}' => Tokens::BLOCK_TYPE_CURLY_BRACE,
    ];

    /**
     * @var string[]
     */
    private $startEdges = ['(', '[', '{'];

    public function findInTokensByEdge(Tokens $tokens, int $position): BlockStartAndEndInfo
    {
        $token = $tokens[$position];

        // shift "array" to "(", event its position
        if ($token->isGivenKind(T_ARRAY)) {
            $position = $tokens->getNextMeaningfulToken($position);
            $token = $tokens[$position];
        }

        $blockType = $this->getBlockTypeByToken($token);

        if (in_array($token->getContent(), $this->startEdges, true)) {
            $blockStart = $position;
            $blockEnd = $tokens->findBlockEnd($blockType, $blockStart);
        } else {
            $blockEnd = $position;
            $blockStart = $tokens->findBlockStart($blockType, $blockEnd);
        }

        return new BlockStartAndEndInfo($blockStart, $blockEnd);
    }

    public function findInTokensByPositionAndContent(
        Tokens $tokens,
        int $position,
        string $content
    ): ?BlockStartAndEndInfo {
        $blockStart = $tokens->getNextTokenOfKind($position, [$content]);
        if ($blockStart === null) {
            return null;
        }

        $blockType = $this->getBlockTypeByContent($content);

        return new BlockStartAndEndInfo($blockStart, $tokens->findBlockEnd($blockType, $blockStart));
    }

    private function getBlockTypeByContent(string $content): int
    {
        if (isset($this->contentToBlockType[$content])) {
            return $this->contentToBlockType[$content];
        }

        throw new MissingImplementationException(sprintf(
            'Implementation is missing for "%s" in "%s". Just add it to "%s" property with proper block type',
            $content,
            __METHOD__,
            '$contentToBlockType'
        ));
    }

    private function getBlockTypeByToken(Token $token): int
    {
        if ($token->isArray()) {
            if (in_array($token->getContent(), ['[', ']'], true)) {
                return Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE;
            } else {
                return Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE;
            }
        }

        return $this->getBlockTypeByContent($token->getContent());
    }
}
