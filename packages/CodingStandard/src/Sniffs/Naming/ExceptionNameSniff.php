<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ExceptionNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Exception should have suffix "Exception".';

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
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_EXTENDS];
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
        $this->tokens = $file->getTokens();

        if (! $this->isException()) {
            return;
        }

        $exceptionName = $this->getExceptionName();
        if (Strings::endsWith($exceptionName, 'Exception')) {
            return;
        }

        if ($file->addFixableError(self::ERROR_MESSAGE, $this->getExceptionNamePosition(), self::class)) {
            $this->fix();
        }
    }

    private function isException(): bool
    {
        $parentClassNamePosition = $this->file->findNext([T_STRING], $this->position);
        $parentClassNameToken = $this->tokens[$parentClassNamePosition];

        return Strings::endsWith($parentClassNameToken['content'], 'Exception');
    }

    private function getExceptionName(): string
    {
        $classNameToken = $this->tokens[$this->getExceptionNamePosition()];

        return $classNameToken['content'];
    }

    private function getExceptionNamePosition(): int
    {
        return (int) $this->file->findPrevious(T_STRING, $this->position);
    }

    private function fix(): void
    {
        $this->fixer->addContent($this->getExceptionNamePosition(), 'Exception');
    }
}
