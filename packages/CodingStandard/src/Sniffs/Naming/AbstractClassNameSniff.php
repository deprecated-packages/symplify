<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
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
     * @return int[]
     */
    public function register(): array
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

        if ($this->shouldBeSkipped()) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function shouldBeSkipped(): bool
    {
        if (! $this->isClassAbstract()) {
            return true;
        }

        if (Strings::startsWith($this->getClassName(), 'Abstract')) {
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
}
