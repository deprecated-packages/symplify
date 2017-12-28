<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
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
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        if (Strings::endsWith($this->getInterfaceName(), 'Interface')) {
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

        $name = $this->file->fixer->getTokenContent($interfaceNamePosition);

        if ($this->isIPrefixedName($name)) {
            $name = substr($name, 1);
        }

        $this->file->fixer->replaceToken($interfaceNamePosition, $name . 'Interface');
    }

    private function isIPrefixedName(string $name): bool
    {
        return strlen($name) >= 3 && $name[0] === 'I' && ctype_upper($name[1]) && ctype_lower($name[2]);
    }
}
