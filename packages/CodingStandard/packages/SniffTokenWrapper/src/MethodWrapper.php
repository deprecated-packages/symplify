<?php declare(strict_types=1);

namespace Symplify\CodingStandard\SniffTokenWrapper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class MethodWrapper
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
     * @var string
     */
    private $methodName;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        $namePointer = TokenHelper::findNextEffective($file, $this->position + 1);
        $this->methodName = $this->tokens[$namePointer]['content'];
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        return new self($file, $position);
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getName(): string
    {
        return $this->methodName;
    }

    public function isPublic(): bool
    {
        $visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective(
            $this->file,
            $this->position - 1
        );

        $visibilityModifiedToken = $this->tokens[$visibilityModifiedTokenPointer];

        return $visibilityModifiedToken['code'] === T_PUBLIC;
    }
}
