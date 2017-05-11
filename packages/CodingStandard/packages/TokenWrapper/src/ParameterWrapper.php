<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use PHP_CodeSniffer\Files\File;

final class ParameterWrapper
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
    private $tokens = [];

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;

        $this->tokens = $this->file->getTokens();
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        return new self($file, $position);
    }

    public function getParameterName(): string
    {
        $namePosition = $this->file->findNext(T_STRING, $this->position);

        return $this->tokens[$namePosition]['content'];
    }

    public function getParameterType(): string
    {
        $typePosition = $this->file->findPrevious(T_STRING, $this->position);

        return $this->tokens[$typePosition]['content'];
    }
}
