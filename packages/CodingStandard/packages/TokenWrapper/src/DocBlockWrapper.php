<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use Symplify\CodingStandard\Helper\ContentFinder;

/**
 * Note: consider refactoring to PHP-CS-Fixer:
 * https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/src/DocBlock/DocBlock.php.
 */
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
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var Fixer
     */
    private $fixer;

    private function __construct(File $file, int $startPosition, int $endPosition)
    {
        $this->file = $file;
        $this->fixer = $file->fixer;
        $this->tokens = $file->getTokens();
        $this->startPosition = $startPosition;
        $this->endPosition = $endPosition;
    }

    public static function createFromFileAndPosition(File $file, int $startPosition, int $endPosition): self
    {
        return new self($file, $startPosition, $endPosition);
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
        $shortPosition = (int) $this->file->findNext($empty, $this->startPosition + 1, $this->endPosition, true);

        // indent content after /** to indented new line
        $this->fixer->addContentBefore($shortPosition, PHP_EOL . $this->getIndentationSign() . ' * ');

        // remove spaces
        $this->fixer->replaceToken($this->startPosition + 1, '');
        $spacelessContent = trim($this->tokens[$this->endPosition - 1]['content']);
        $spacelessContent = $this->processMultipleAnnotations($spacelessContent);
        $this->fixer->replaceToken($this->endPosition - 1, $spacelessContent);

        // indent end to indented newline
        $this->fixer->replaceToken($this->endPosition, PHP_EOL . $this->getIndentationSign() . ' */');
    }

    public function getAnnotationValue(string $annotation): string
    {
        $docBlockTokens = ContentFinder::getTokensBetween($this->file, $this->startPosition, $this->endPosition);
        foreach ($docBlockTokens as $position => $content) {
            if ($content === $annotation) {
                return $docBlockTokens[$position + 2];
            }
        }

        return '';
    }

    private function getIndentationSign(): string
    {
        if ($this->indentationType === 'tabs') {
            return "\t";
        }

        return '    ';
    }

    private function processMultipleAnnotations(string $content): string
    {
        if (! Strings::contains($content, '@')) {
            return $content;
        }

        $contentLines = explode('@', $content);
        $multilineContent = trim(array_shift($contentLines));
        foreach ($contentLines as $contentLine) {
            $multilineContent .= PHP_EOL . '     * @' . $contentLine;
        }

        return $multilineContent;
    }
}
