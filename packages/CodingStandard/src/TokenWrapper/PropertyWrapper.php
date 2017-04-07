<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use PHP_CodeSniffer\Files\File;
use Symplify\CodingStandard\Helper\TokenFinder;

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
    private $propertyToken;

    /**
     * @var mixed[]
     */
    private $tokens;

    /**
     * @var ?int
     */
    private $accessibilityPosition;

    private function __construct(File $file, int $position)
    {
        // todo: move these 4 to abstract + program against interface!
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
        return $this->getDocBlock()
            ->hasAnnotation($annotation);
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
        $phpDocCommentCloseTokenPosition = $this->getDocCommentCloseTokenPosition();

        if (! $this->hasDocComment($phpDocCommentCloseTokenPosition)) {
            return false;
        }

        $findPhpDocTagPointer = $this->file->findPrevious(
            T_DOC_COMMENT_OPEN_TAG, $phpDocCommentCloseTokenPosition - 1
        ) + 1;

        return DocBlockWrapper::createFromFileAndPosition(
            $this->file,
            $findPhpDocTagPointer - 1,
            $phpDocCommentCloseTokenPosition
        );
    }

    public function removeAnnotation(string $annotation): void
    {
        $this->getDocBlock()
            ->removeAnnotation($annotation);

        // here use fixer
        // todo1: remove before/after spaces
        /* @var Type @inject */
    }

    public function changeAccesibilityToPrivate(): void
    {
        $accesiblityPosition = $this->getPropertyAccessibilityPosition();
        if ($accesiblityPosition) {
            $file = $this->file;
            $fixer = $file->fixer;
            $fixer->replaceToken($accesiblityPosition, 'private');
        }
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

    private function getPropertyAccessibilityPosition(): ?int
    {
        if ($this->accessibilityPosition) {
            return $this->accessibilityPosition;
        }

        $visibilityModifiedTokenPointer = TokenFinder::findPreviousEffective(
            $this->file,
            $this->position - 1
        );
        $visibilityModifiedToken = $this->tokens[$visibilityModifiedTokenPointer];

        $accesibility = [];

        if (in_array($visibilityModifiedToken['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
            $accesibility = [
                $visibilityModifiedTokenPointer => $visibilityModifiedToken['code']
            ];
        }

        return $this->accessibilityPosition = key($accesiblity);
    }

    /**
     * @return bool|int
     */
    private function getDocCommentCloseTokenPosition()
    {
        $visibilityModifiedTokenPointer = TokenFinder::findPreviousEffective(
            $this->file,
            $this->position - 1
        );

        return TokenFinder::findPreviousExcluding(
            $this->file, [T_WHITESPACE], $visibilityModifiedTokenPointer - 1
        );
    }

    private function hasDocComment(int $phpDocCommentCloseTokenPosition): bool
    {
        $phpDocCommentCloseToken = $this->tokens[$phpDocCommentCloseTokenPosition];

        return $phpDocCommentCloseToken['code'] === T_DOC_COMMENT_CLOSE_TAG;
    }
}
