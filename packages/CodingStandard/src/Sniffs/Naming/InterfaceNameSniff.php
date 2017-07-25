<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
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
     * @var Fixer
     */
    private $fixer;

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
        $this->fixer = $file->fixer;
        $this->position = $position;

        $interfaceName = $this->getInterfaceName();
        if (Strings::endsWith($interfaceName, 'Interface')) {
            return;
        }

        if ($file->addFixableError(self::ERROR_MESSAGE, $position, self::class)) {
            $this->fix();
        }
    }

    private function getInterfaceName(): string
    {
        return (string) $this->file->getDeclarationName($this->position);
    }

    private function getInterfaceNamePosition(): int
    {
        return (int) $this->file->findNext(T_STRING, $this->position);
    }

    private function fix(): void
    {
        $interfaceNamePosition = $this->getInterfaceNamePosition();

        $this->fixer->addContent($interfaceNamePosition, 'Interface');
    }
}
