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
     * @var array
     */
    private $propertyToken;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @var array
     */
    private $accessibility;

    /**
     * @var DocBlockWrapper|false
     */
    private $docBlock;

    private function __construct(File $file, int $position)
    {
        // todo: move these 4 to abstract + program against interface!
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $this->file->getTokens();
        $this->propertyToken = $this->tokens[$position];
    }

    public static function createFromFileAndPosition(File $file, int $position)
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
        if ($this->docBlock) {
            return $this->docBlock;
        }

        $visibilityModifiedTokenPointer = TokenFinder::findPreviousEffective($this->file, $this->position - 1);
        $tokens = $this->file->getTokens();

        $phpDocTokenCloseTagPointer = TokenFinder::findPreviousExcluding(
            $this->file, [T_WHITESPACE], $visibilityModifiedTokenPointer - 1
        );
        $phpDocTokenCloseTag = $tokens[$phpDocTokenCloseTagPointer];

        // has no doc comment
        if ($phpDocTokenCloseTag['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            $this->docBlock = false;
        }

        $findPhpDocTagPointer = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $phpDocTokenCloseTagPointer - 1) + 1;
        $this->docBlock = DocBlockWrapper::createFromFileAndPosition(
            $this->file,
            $findPhpDocTagPointer - 1,
            $phpDocTokenCloseTagPointer
        );

        return $this->docBlock;
    }

    public function removeAnnotation(string $annotation): void
    {
        $this->getDocBlock()
            ->removeAnnotation($annotation);

        // here use fixer
        // todo1: remove before/after spaces
        /** @var Type @inject */
    }

    public function changeAccesibilityToPrivate(): void
    {
        $accesiblity = $this->getPropertyAccessibility();
        $accesiblityPosition = key($accesiblity);
        if ($accesiblityPosition) {
            $this->file->fixer->replaceToken($accesiblityPosition, 'private');
        }
    }

    public function getType(): string
    {
        return $this->docBlock->getAnnotationValue('@var');
    }

    public function getName(): string
    {
        return ltrim($this->propertyToken['content'], '$');
    }

    private function getPropertyAccessibility(): array
    {
        if ($this->accessibility) {
            return $this->accessibility;
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

        return $this->accessibility = $accesibility;
    }
}
