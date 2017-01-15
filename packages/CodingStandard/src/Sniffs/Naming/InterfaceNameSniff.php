<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Rules:
 * - Interface should have suffix "Interface".
 */
final class InterfaceNameSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'Symplify\CodingStandard.Naming.InterfaceName';

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
        return [T_INTERFACE];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;

        $interfaceName = $this->getInterfaceName();
        if ((strlen($interfaceName) - strlen('Interface')) === strrpos($interfaceName, 'Interface')) {
            return;
        }

        $fix = $file->addFixableError(
            'Interface should have suffix "Interface".',
            $position,
            null
        );

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

    private function fix() : void
    {
        $interfaceNamePosition = $this->getInterfaceNamePosition();

        $this->file->fixer->addContent($interfaceNamePosition, 'Interface');
    }
}
