<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

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

        $namePosition = $file->findNext(T_STRING, $this->position + 1);
        $this->methodName = $this->tokens[$namePosition]['content'];
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        TokenTypeGuard::ensureIsTokenType($file->getTokens()[$position], [T_FUNCTION], __METHOD__);

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
}
