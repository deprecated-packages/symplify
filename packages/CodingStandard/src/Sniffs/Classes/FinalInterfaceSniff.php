<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;

final class FinalInterfaceSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Non-abstract class that implements interface should be final.';

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->fixer = $file->fixer;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $fix = $file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
        if ($fix) {
            $this->addFinalToClass($position);
        }
    }

    public function addFinalToClass(int $position): void
    {
        $this->fixer->addContentBefore($position, 'final ');
    }

    private function shouldBeSkipped(): bool
    {
        if ($this->implementsInterface() === false) {
            return true;
        }

        if ($this->isFinalOrAbstractClass()) {
            return true;
        }

        if ($this->isDoctrineEntity()) {
            return true;
        }

        return false;
    }

    private function implementsInterface(): bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position);
    }

    private function isFinalOrAbstractClass(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'] || $classProperties['is_final'];
    }

    private function isDoctrineEntity(): bool
    {
        $docCommentPosition = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position);
        if ($docCommentPosition === false) {
            return false;
        }

        $seekPosition = $docCommentPosition;

        do {
            $docCommentTokenContent = $this->file->getTokens()[$docCommentPosition]['content'];
            if (strpos($docCommentTokenContent, 'Entity') !== false) {
                return true;
            }
            ++$seekPosition;
        } while ($docCommentPosition = $this->file->findNext(T_DOC_COMMENT_TAG, $seekPosition, $this->position));

        return false;
    }
}
