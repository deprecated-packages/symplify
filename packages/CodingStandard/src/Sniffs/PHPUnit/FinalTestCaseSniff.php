<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\PHPUnit;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;

final class FinalTestCaseSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Non-abstract class that extends TestCase should be final.';

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

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_CLASS];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();
        $this->fixer = $file->fixer;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $fix = $file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
        if ($fix) {
            $this->addFinalToClass($position);
        }
    }

    private function shouldBeSkipped(): bool
    {
        if (! $this->extendsTestCase()) {
            return true;
        }

        if ($this->isFinalOrAbstractClass()) {
            return true;
        }

        return false;
    }

    private function extendsTestCase(): bool
    {
        if (! (bool) $this->file->findNext(T_EXTENDS, $this->position)) {
            return false;
        }

        $parentClassPosition = $this->file->findNext(T_STRING, $this->position + 3);
        $parentClassName = $this->tokens[$parentClassPosition]['content'];

        if (! Strings::contains($parentClassName, 'TestCase')) {
            return false;
        }

        return true;
    }

    private function isFinalOrAbstractClass(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'] || $classProperties['is_final'];
    }

    private function addFinalToClass(int $position): void
    {
        $this->fixer->addContentBefore($position, 'final ');
    }
}
