<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ClassWrapper
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    private function __construct(File $file, int $position)
    {
        TokenTypeGuard::ensureIsTokenType($file->getTokens()[$position], [T_CLASS, T_TRAIT, T_INTERFACE], __METHOD__);

        $this->file = $file;
        $this->position = $position;
    }

    public static function createFromFirstClassInFile(File $file): ?self
    {
        $possibleClassPosition = $file->findNext(T_CLASS, 0);
        if (! is_int($possibleClassPosition)) {
            return null;
        }

        return new self($file, $possibleClassPosition);
    }

    public function getClassName(): string
    {
        return Naming::getClassName($this->file, $this->position + 2);
    }

    public function implementsInterface(): bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position, $this->position + 15);
    }

    public function extends(): bool
    {
        return (bool) $this->file->findNext(T_EXTENDS, $this->position, $this->position + 5);
    }

    public function getParentClassName(): ?string
    {
        $extendsTokenPosition = TokenHelper::findNext($this->file, T_EXTENDS, $this->position, $this->position + 10);
        if ($extendsTokenPosition === null) {
            return null;
        }

        $parentClassPosition = (int) TokenHelper::findNext($this->file, T_STRING, $extendsTokenPosition);

        return Naming::getClassName($this->file, $parentClassPosition);
    }
}
