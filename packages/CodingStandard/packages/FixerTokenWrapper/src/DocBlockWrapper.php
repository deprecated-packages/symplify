<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

final class DocBlockWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var int
     */
    private $docBlockPosition;

    private function __construct(Tokens $tokens, int $docBlockPosition, DocBlock $docBlock)
    {
        $this->tokens = $tokens;
        $this->docBlockPosition = $docBlockPosition;
        $this->docBlock = $docBlock;
    }

    public static function createFromTokensPositionAndDocBlock(
        Tokens $tokens,
        int $docBlockPosition,
        DocBlock $docBlock
    ): self {
        return new self($tokens, $docBlockPosition, $docBlock);
    }

    public function isSingleLine(): bool
    {
        return count($this->docBlock->getLines()) === 1;
    }

    public function changeToMultiLine(): void
    {
        $indent = $this->whitespacesFixerConfig->getIndent();
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();
        $newLineWithIndent = $lineEnding . $indent;

        $newDocBlock = str_replace(
            [' @', '/** ', ' */'],
            [
                $newLineWithIndent . ' * @',
                '/**',
                $newLineWithIndent . ' */',
            ],
            $this->docBlock->getContent()
        );

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $newDocBlock]);
    }

    public function setWhitespacesFixerConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }
}
