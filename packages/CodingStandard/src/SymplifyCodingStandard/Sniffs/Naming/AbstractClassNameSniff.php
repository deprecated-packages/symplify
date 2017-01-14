<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Rules:
 * - Abstract class should have prefix "Abstract".
 */
final class AbstractClassNameSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Naming.AbstractClassName';

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
        return [T_CLASS];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;

        if (! $this->isClassAbstract()) {
            return;
        }

        if (strpos($this->getClassName(), 'Abstract') === 0) {
            return;
        }

        $fix = $file->addFixableError('Abstract class should have prefix "Abstract".', $position);

        if ($fix === true) {
            $this->fix();
        }
    }

    private function isClassAbstract() : bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'];
    }

    /**
     * @return string|false
     */
    private function getClassName()
    {
        $namePosition = $this->file->findNext(T_STRING, $this->position);
        if (! $namePosition) {
            return false;
        }

        return $this->file->getTokens()[$namePosition]['content'];
    }

    private function fix() : void
    {
        $this->file->fixer->addContent($this->position + 1, 'Abstract');
    }
}
