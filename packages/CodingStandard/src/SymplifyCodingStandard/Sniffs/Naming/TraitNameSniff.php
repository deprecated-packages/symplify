<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Rules:
 * - Trait should have suffix "Trait".
 */
final class TraitNameSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Naming.TraitName';

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_TRAIT];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;

        $interfaceName = $this->getTraitName();
        if ((strlen($interfaceName) - strlen('Trait')) === strrpos($interfaceName, 'Trait')) {
            return;
        }

        $fix = $file->addFixableError('Trait should have suffix "Trait".', $position);

        if ($fix === true) {
            $this->fix();
        }
    }

    /**
     * @return string|false
     */
    private function getTraitName()
    {
        $namePosition = $this->getTraitNamePosition();
        if (! $namePosition) {
            return false;
        }

        return $this->file->getTokens()[$namePosition]['content'];
    }

    /**
     * @return bool|int
     */
    private function getTraitNamePosition()
    {
        return $this->file->findNext(T_STRING, $this->position);
    }

    private function fix() : void
    {
        $this->file->fixer->addContent($this->getTraitNamePosition(), 'Trait');
    }
}
