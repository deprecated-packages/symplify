<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;

final class DocBlockWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var int
     */
    private $position;

    /**
     * @var PhpDocInfo
     */
    private $phpDocInfo;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    public function __construct(
        Tokens $tokens,
        int $position,
        PhpDocInfo $phpDocInfo,
        PhpDocInfoPrinter $phpDocInfoPrinter
    ) {
        $this->tokens = $tokens;
        $this->position = $position;
        $this->phpDocInfo = $phpDocInfo;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
    }

    public function getTokenPosition(): int
    {
        return $this->position;
    }

    public function isSingleLine(): bool
    {
        return substr_count($this->phpDocInfo->getOriginalContent(), PHP_EOL) < 1;
    }

    public function getPhpDocInfo(): PhpDocInfo
    {
        return $this->phpDocInfo;
    }

    /**
     * @return string[]
     */
    public function getArgumentType(string $name): array
    {
        return $this->phpDocInfo->getParamTypes($name);
    }

    public function getArgumentTypeNode(string $name): ?TypeNode
    {
        return $this->phpDocInfo->getParamTypeNode($name);
    }

    /**
     * @return string[]
     */
    public function getVarTypes(): array
    {
        return $this->phpDocInfo->getVarTypes();
    }

    public function getParamTagDescription(string $name): string
    {
        return $this->phpDocInfo->getParamTagDescriptionByName($name);
    }

    public function removeReturnType(): void
    {
        $this->phpDocInfo->removeReturnTag();
    }

    public function removeParamType(string $name): void
    {
        $this->phpDocInfo->removeParamTagByParameter($name);
    }

    public function isArrayProperty(): bool
    {
        $varTagValue = $this->phpDocInfo->getVarTagValue();
        if ($varTagValue === null) {
            return false;
        }

        return $this->isIterableType($varTagValue->type);
    }

    public function getContent(): string
    {
        return $this->phpDocInfoPrinter->printFormatPreserving($this->phpDocInfo);
    }

    public function saveNewPhpDocInfo(): void
    {
        $newDocCommentContent = $this->phpDocInfoPrinter->printFormatPreserving($this->phpDocInfo);
        if ($newDocCommentContent) {
            // create and save new doc comment
            $this->tokens[$this->position] = new Token([T_DOC_COMMENT, $newDocCommentContent]);
            return;
        }

        // remove empty doc
        $this->tokens->clearAt($this->position);
        if ($this->tokens[$this->position - 1]->isWhitespace()) {
            // used from RemoveEmptyDocBlockFixer
            $this->removeExtraWhitespaceAfterRemovedDocBlock();
        }
    }

    private function removeExtraWhitespaceAfterRemovedDocBlock(): void
    {
        $previousToken = $this->tokens[$this->position - 1];
        if ($previousToken->isWhitespace()) {
            $previousWhitespaceContent = $previousToken->getContent();

            $lastLineBreak = strrpos($previousWhitespaceContent, PHP_EOL);
            $newWhitespaceContent = Strings::substring($previousWhitespaceContent, 0, $lastLineBreak);
            if ($newWhitespaceContent) {
                $this->tokens[$this->position - 1] = new Token([T_WHITESPACE, $newWhitespaceContent]);
            } else {
                $this->tokens->clearAt($this->position - 1);
            }
        }
    }

    /**
     * @todo move to some Analyzer
     */
    private function isIterableType(TypeNode $typeNode): bool
    {
        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $subType) {
                if (! $this->isIterableType($subType)) {
                    return false;
                }
            }

            return true;
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            return $typeNode->name === 'array';
        }

        return $typeNode instanceof ArrayTypeNode;
    }
}
