<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use Symplify\CodingStandard\Helper\ContentFinder;

final class DocBlockWrapper
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $startPosition;

    /**
     * @var int
     */
    private $endPosition;

    /**
     * @var string
     */
    private $indentationType = 'spaces';

    /**
     * @var string[]
     */
    private $tokens;

    private function __construct(File $file, int $startPosition, int $endPosition)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->startPosition = $startPosition;
        $this->endPosition = $endPosition;
    }

    public static function createFromFileAndPosition(File $file, int $startPosition, int $endPosition)
    {
        return new self($file, $startPosition, $endPosition);
    }

    public function hasAnnotation(string $annotation): bool
    {
        $docBlockContent = ContentFinder::getContentBetween($this->file, $this->startPosition, $this->endPosition);

        return Strings::contains($docBlockContent, $annotation);
    }

    public function removeAnnotation(string $annotation): void
    {
        $docBlockTokens = ContentFinder::getTokensBetween($this->file, $this->startPosition, $this->endPosition);

        foreach ($docBlockTokens as $position => $content) {
            if ($content === $annotation) {
                $this->file->fixer->replaceToken($position, '');

                // cleanup spaces
                $cleanupPosition = $position;
                while ($docBlockTokens[$cleanupPosition] !== "\n") {
                    $cleanupPosition--;
                    $this->file->fixer->replaceToken($cleanupPosition, '');
                }
            }
        }
    }

    public function isSingleLine(): bool
    {
        $tokens = $this->file->getTokens();
        return $tokens[$this->startPosition]['line'] === $tokens[$this->endPosition]['line'];
    }

    public function changeToMultiLine(): void
    {
        if (! $this->isSingleLine()) {
            return;
        }

        $empty = [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR];
        $shortPosition = $this->file->findNext($empty, $this->startPosition + 1, $this->endPosition, true);

        // indent content after /** to indented new line
        $this->file->fixer->addContentBefore(
            $shortPosition,
            PHP_EOL . $this->getIndentationSign() . ' * '
        );

        // remove spaces
        $this->file->fixer->replaceToken($this->startPosition + 1, '');
        $spacelessContent = trim($this->tokens[$this->endPosition - 1]['content']);
        $this->file->fixer->replaceToken($this->endPosition - 1, $spacelessContent);

        // indent end to indented newline
        $this->file->fixer->replaceToken(
            $this->endPosition,
            PHP_EOL . $this->getIndentationSign() . ' */'
        );
    }

    /**
     * @return string|false
     */
    public function getAnnotationValue(string $annotation)
    {
        $docBlockTokens = ContentFinder::getTokensBetween($this->file, $this->startPosition, $this->endPosition);
        foreach ($docBlockTokens as $position => $content) {
            if ($content === $annotation) {
                return $docBlockTokens[$position + 2];
            }
        }

        return false;
    }

    private function getIndentationSign(): string
    {
        if ($this->indentationType === 'tabs') {
            return "\t";
        }

        return '    ';
    }
}
