<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\Utils\Strings;
use PHP_CodeSniffer_File;
use Symplify\CodingStandard\Helper\ContentFinder;
use Symplify\CodingStandard\Helper\TokenFinder;

final class DocBlockWrapper
{
    /**
     * @var PHP_CodeSniffer_File
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

    public static function createFromFileAndPosition(PHP_CodeSniffer_File $file, int $startPosition, int $endPosition)
    {
        return new self($file, $startPosition, $endPosition);
    }

    private function __construct(PHP_CodeSniffer_File $file, int $startPosition, int $endPosition)
    {
        $this->file = $file;
        $this->startPosition = $startPosition;
        $this->endPosition = $endPosition;
    }

    public function hasAnnotation(string $annotation) : bool
    {
        $docBlockContent = ContentFinder::getContentBetween($this->file, $this->startPosition, $this->endPosition);

        return Strings::contains($docBlockContent, $annotation);
    }

    public function removeAnnotation(string $annotation)
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
}
