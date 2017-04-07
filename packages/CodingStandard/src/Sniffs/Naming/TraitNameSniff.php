<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;

final class TraitNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Trait should have suffix "Trait".';

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
        return [T_TRAIT];
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

        $interfaceName = $this->getTraitName();
        if ((strlen($interfaceName) - strlen('Trait')) === strrpos($interfaceName, 'Trait')) {
            return;
        }

        if ($file->addFixableError(self::ERROR_MESSAGE, $position, self::class)) {
            $this->fix();
        }
    }

    private function getTraitName(): string
    {
        $namePosition = $this->getTraitNamePosition();

        return $this->file->getTokens()[$namePosition]['content'];
    }

    private function getTraitNamePosition(): int
    {
        return (int) $this->file->findNext(T_STRING, $this->position);
    }

    private function fix(): void
    {
        $this->fixer->addContent($this->getTraitNamePosition(), 'Trait');
    }
}
