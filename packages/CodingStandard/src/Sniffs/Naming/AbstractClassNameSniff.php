<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;

final class AbstractClassNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Abstract class should have prefix "Abstract".';

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
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
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

        if ($this->shouldBeSkipped()) {
            return;
        }

        $fix = $file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
        if ($fix === true) {
            $this->fix();
        }
    }

    private function shouldBeSkipped(): bool
    {
        if (! $this->isClassAbstract()) {
            return true;
        }

        if (strpos($this->getClassName(), 'Abstract') === 0) {
            return true;
        }

        return false;
    }

    private function isClassAbstract(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'];
    }

    private function getClassName(): ?string
    {
        return $this->file->getDeclarationName($this->position);
    }

    private function fix(): void
    {
        $this->fixer->addContent($this->position + 1, 'Abstract');
    }
}
