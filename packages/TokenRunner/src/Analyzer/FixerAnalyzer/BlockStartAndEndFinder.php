<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Exception\MissingImplementationException;

final class BlockStartAndEndFinder
{
    /**
     * @var int[]
     */
    private $contentToBlockType = [
        '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        '[' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
    ];

    public function findInTokensByBlockStart(Tokens $tokens, int $blockStart): BlockStartAndEndInfo
    {
        $token = $tokens[$blockStart];

        // shift "array" to "("
        if ($token->isGivenKind(T_ARRAY)) {
            $blockStart = $tokens->getNextMeaningfulToken($blockStart);
            $token = $tokens[$blockStart];
        }

        // @todo: shift "function" to its "("?
        $blockType = $this->getBlockTypeByContent($token->getContent());

        return new BlockStartAndEndInfo($blockStart, $tokens->findBlockEnd($blockType, $blockStart));
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
            'Implementation is missing for "%s" in "%s". Just add it to "%s" property with proper bock type',
            $content,
            __METHOD__,
            '$contentToBlockType'
        ));
    }
}
