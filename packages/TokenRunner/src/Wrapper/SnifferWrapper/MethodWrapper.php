<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class MethodWrapper
{
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
        $this->position = $position;
        $this->tokens = $file->getTokens();
        $this->methodName = $this->resolveMethodNameFromPosition($file, $this->position + 1);
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

    private function resolveMethodNameFromPosition(File $file, int $position): string
    {
        $namePosition = $file->findNext(T_STRING, $position);

        return $this->tokens[$namePosition]['content'];
    }
}
