<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class InterfaceNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Interface should have suffix "Interface".';

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
    public function register(): array
    {
        return [T_INTERFACE];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        $interfaceName = $this->getInterfaceName();
        if ((strlen($interfaceName) - strlen('Interface')) === strrpos($interfaceName, 'Interface')) {
            return;
        }

        if ($file->addFixableError(self::ERROR_MESSAGE, $position, self::class)) {
            $this->fix();
        }
    }

    private function getInterfaceName(): string
    {
        $namePosition = $this->getInterfaceNamePosition();

        return $this->file->getTokens()[$namePosition]['content'];
    }

    private function getInterfaceNamePosition(): int
    {
        return (int) $this->file->findNext(T_STRING, $this->position);
    }

    private function fix(): void
    {
        $interfaceNamePosition = $this->getInterfaceNamePosition();

        $this->file->fixer->addContent($interfaceNamePosition, 'Interface');
    }
}
