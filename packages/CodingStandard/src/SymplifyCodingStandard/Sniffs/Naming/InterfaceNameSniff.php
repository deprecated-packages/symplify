<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Naming;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Rules:
 * - Interface should have suffix "Interface".
 */
final class InterfaceNameSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var PHP_CodeSniffer_File
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
        return [T_INTERFACE];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;

        $interfaceName = $this->getInterfaceName();
        if ((strlen($interfaceName) - strlen('Interface')) === strrpos($interfaceName, 'Interface')) {
            return;
        }

        $fix = $file->addFixableError('Interface should have suffix "Interface".', $position);

        if ($fix === true) {
            $this->fix();
        }
    }

    /**
     * @return string|false
     */
    private function getInterfaceName()
    {
        $namePosition = $this->getInterfaceNamePosition();
        if (! $namePosition) {
            return false;
        }

        return $this->file->getTokens()[$namePosition]['content'];
    }

    /**
     * @return bool|int
     */
    private function getInterfaceNamePosition()
    {
        return $this->file->findNext(T_STRING, $this->position);
    }

    private function fix()
    {
        $interfaceNamePosition = $this->getInterfaceNamePosition();

        $this->file->fixer->beginChangeset();
        $this->file->fixer->addContent($interfaceNamePosition, 'Interface');
        $this->file->fixer->endChangeset();
    }
}
