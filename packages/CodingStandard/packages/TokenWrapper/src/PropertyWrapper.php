<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class PropertyWrapper
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var mixed[]
     */
    private $propertyToken = [];

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var ?int
     */
    private $accessibilityPosition;

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $this->file->getTokens();
        $this->propertyToken = $this->tokens[$position];
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        return new self($file, $position);
    }

    public function hasAnnotation(string $annotation): bool
    {
        $docBlock = $this->getDocBlock();

        if (! $docBlock instanceof DocBlockWrapper) {
            return false;
        }

        return $docBlock->hasAnnotation($annotation);
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return DocBlockWrapper|false
     */
    public function getDocBlock()
    {
        if (! $this->hasDocBlock()) {
            return false;
        }

        $docBlockClosePosition = $this->getDocCommentCloseTokenPosition();
        $docBlockOpenPosition = $this->file->findPrevious(
            T_DOC_COMMENT_OPEN_TAG, $docBlockClosePosition - 1
        ) + 1;

        return DocBlockWrapper::createFromFileAndPosition(
            $this->file,
            $docBlockOpenPosition - 1,
            $docBlockClosePosition
        );
    }

    public function changeAccesibilityToPrivate(): void
    {
        $accesiblityPosition = $this->getPropertyAccessibilityPosition();
        if ($accesiblityPosition === false) {
            return;
        }

        $file = $this->file;
        $fixer = $file->fixer;
        $fixer->replaceToken($accesiblityPosition, 'private');
    }

    public function getType(): string
    {
        return $this->getDocBlock()
            ->getAnnotationValue('@var');
    }

    public function getName(): string
    {
        return ltrim($this->propertyToken['content'], '$');
    }

    /**
     * @return false|int
     */
    private function getPropertyAccessibilityPosition()
    {
        if ($this->accessibilityPosition) {
            return $this->accessibilityPosition;
        }

        $visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective(
            $this->file,
            $this->position - 1
        );

        $visibilityModifiedToken = $this->tokens[$visibilityModifiedTokenPointer];

        if (in_array($visibilityModifiedToken['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
            return (int) $visibilityModifiedTokenPointer;
        }

        return false;
    }

    /**
     * @return false|int
     */
    private function getDocCommentCloseTokenPosition()
    {
        $visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective(
            $this->file,
            $this->position - 1
        );

        return TokenHelper::findPreviousExcluding(
            $this->file, [T_WHITESPACE], $visibilityModifiedTokenPointer - 1
        );
    }

    private function hasDocBlock(): bool
    {
        $phpDocCommentCloseTokenPosition = $this->getDocCommentCloseTokenPosition();
        if ($phpDocCommentCloseTokenPosition === false) {
            return false;
        }

        if (! $this->hasDocComment($phpDocCommentCloseTokenPosition)) {
            return false;
        }

        return true;
    }

    private function hasDocComment(int $phpDocCommentCloseTokenPosition): bool
    {
        $phpDocCommentCloseToken = $this->tokens[$phpDocCommentCloseTokenPosition];

        return $phpDocCommentCloseToken['code'] === T_DOC_COMMENT_CLOSE_TAG;
    }
}
